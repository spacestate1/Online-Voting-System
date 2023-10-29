#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <time.h>
#include <libpq-fe.h>

// Database Configuration
const char *conninfo = "dbname=votesystem user=system password=system-pass host=localhost port=5432";

// Data structures
typedef struct {
    char title[255];
    int approve_count;
    int deny_count;
    char election_name[255];
    char election_start_date[255];
} ActionItem;

typedef struct {
    char name[255];
    char has_voted[5];
} VoterData;

typedef struct {
    char title[255];
    char description[2048];
    char voter_name[255];
    char vote[50];
} DetailedData;

void sanitize_filename(char *str) {
    for (; *str; ++str) {
        if (!((*str >= 'a' && *str <= 'z') || (*str >= 'A' && *str <= 'Z') || (*str >= '0' && *str <= '9'))) {
            *str = '_';
        }
    }
}



// Database queries
const char *action_items_query = 
    "SELECT "
    "    ai.title,"
    "    COUNT(CASE WHEN aiv.vote = 'Approved' THEN 1 END) AS approve_count,"
    "    COUNT(CASE WHEN aiv.vote = 'Denied' THEN 1 END) AS deny_count,"
    "    e.name AS election_name,"
    "    e.start_date AS election_start_date "
    "FROM action_items ai "
    "JOIN action_item_votes aiv ON ai.id = aiv.action_item_id "
    "JOIN elections e ON ai.election_id = e.id "
    "WHERE e.name = $1 "
    "GROUP BY ai.title, e.name, e.start_date "
    "ORDER BY ai.title";

const char *total_voters_query = 
    "SELECT COUNT(*) FROM voters;";

const char *voters_data_query = 
    "SELECT "
    "    CONCAT(v.firstname, ' ', v.lastname) AS name,"
    "    CASE WHEN COUNT(av.id) = 0 THEN 'No' ELSE 'Yes' END AS has_voted "
    "FROM voters v "
    "LEFT JOIN action_item_votes av ON v.id = av.voters_id AND av.election_id = (SELECT id FROM elections WHERE name = $1) "
    "GROUP BY v.id "
    "ORDER BY name";

const char *detailed_data_query = 
    "SELECT "
    "    ai.title,"
    "    ai.description,"
    "    CONCAT(v.firstname, ' ', v.lastname) AS voter_name,"
    "    aiv.vote "
    "FROM action_items ai "
    "JOIN action_item_votes aiv ON ai.id = aiv.action_item_id "
    "JOIN elections e ON ai.election_id = e.id "
    "JOIN voters v ON aiv.voters_id = v.id "
    "WHERE e.name = $1 "
    "ORDER BY ai.title, aiv.vote, v.firstname, v.lastname";

void fetch_data(const char *election_name) {
    PGconn *conn = PQconnectdb(conninfo);
    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Connection to database failed: %s", PQerrorMessage(conn));
        PQfinish(conn);
        exit(1);
    }

    // Fetch action items
    PGresult *res = PQexecParams(conn, action_items_query, 1, NULL, &election_name, NULL, NULL, 0);
    int rows = PQntuples(res);
    ActionItem actionItems[rows];
    for (int i = 0; i < rows; i++) {
        strcpy(actionItems[i].title, PQgetvalue(res, i, 0));
        actionItems[i].approve_count = atoi(PQgetvalue(res, i, 1));
        actionItems[i].deny_count = atoi(PQgetvalue(res, i, 2));
        strcpy(actionItems[i].election_name, PQgetvalue(res, i, 3));
        strcpy(actionItems[i].election_start_date, PQgetvalue(res, i, 4));
    }
    PQclear(res);

    // Fetch total voters
    res = PQexec(conn, total_voters_query);
    int total_voters = atoi(PQgetvalue(res, 0, 0));
    PQclear(res);

    // Fetch voters data
    res = PQexecParams(conn, voters_data_query, 1, NULL, &election_name, NULL, NULL, 0);
    rows = PQntuples(res);
    VoterData voters[rows];
    for (int i = 0; i < rows; i++) {
        strcpy(voters[i].name, PQgetvalue(res, i, 0));
        strcpy(voters[i].has_voted, PQgetvalue(res, i, 1));
    }
    PQclear(res);

    // Fetch detailed data
    res = PQexecParams(conn, detailed_data_query, 1, NULL, &election_name, NULL, NULL, 0);
    rows = PQntuples(res);
    DetailedData details[rows];
    for (int i = 0; i < rows; i++) {
        strcpy(details[i].title, PQgetvalue(res, i, 0));
        strcpy(details[i].description, PQgetvalue(res, i, 1));
        strcpy(details[i].voter_name, PQgetvalue(res, i, 2));
        strcpy(details[i].vote, PQgetvalue(res, i, 3));
    }
    PQclear(res);

    PQfinish(conn);

    // Generate output
    char filename[255];

char sanitized_election_name[256]; // Make sure the size matches with election_name parameter
strncpy(sanitized_election_name, election_name, sizeof(sanitized_election_name) - 1);
sanitized_election_name[sizeof(sanitized_election_name) - 1] = '\0'; // Null terminate to be sure
sanitize_filename(sanitized_election_name);
snprintf(filename, sizeof(filename), "%s.txt", sanitized_election_name);
FILE *file = fopen(filename, "w");



    fprintf(file, "Vote Tally Report: %s\n", election_name);
    fprintf(file, "Election Start Date: %s\n", actionItems[0].election_start_date);
    for (int i = 0; i < sizeof(actionItems) / sizeof(actionItems[0]); i++) {
        char result[15];
        if (actionItems[i].approve_count > total_voters / 2) {
            strcpy(result, "Approved");
        } else if (actionItems[i].deny_count > total_voters / 2) {
            strcpy(result, "Denied");
        } else {
            strcpy(result, "Undetermined");
        }
        fprintf(file, "Title: %s, Approve Count: %d, Deny Count: %d, Result: %s\n", actionItems[i].title, actionItems[i].approve_count, actionItems[i].deny_count, result);
    }
    for (int i = 0; i < sizeof(voters) / sizeof(voters[0]); i++) {
        fprintf(file, "Name: %s, Has Voted: %s\n", voters[i].name, voters[i].has_voted);
    }

    char current_title[255] = "";
for (int i = 0; i < sizeof(details) / sizeof(details[0]); i++) {
    if (strcmp(current_title, details[i].title) != 0) {
        fprintf(file, "==========================\n");
        fprintf(file, "Title: %s\n", details[i].title);
        fprintf(file, "Description: %s\n", details[i].description);
        strcpy(current_title, details[i].title);
    }
    fprintf(file, "Voter: %s - %s\n", details[i].voter_name, details[i].vote);
}


    fclose(file);
}

int main(int argc, char **argv) {
    if (argc != 2) {
        printf("Usage: %s <election_name>\n", argv[0]);
        return 1;
    }

    fetch_data(argv[1]);
    return 0;
}
