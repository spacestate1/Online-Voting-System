#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <libpq-fe.h>

void printHelp() {
    printf("Usage: ./action_items_vote [OPTION]\n");
    printf("Retrieve and display data from the PostgreSQL database.\n\n");
    printf("Options:\n");
    printf("  -t    Display a tally of action item votes.\n");
    printf("  -h    Display this help menu.\n");
    printf("  -u    Show how users voted.\n");
}

int main(int argc, char *argv[]) {
    if (argc == 1 || (argc == 2 && strcmp(argv[1], "-h") == 0)) {
        printHelp();
        return 0;
    }
    
    // Define PostgreSQL connection parameters
    const char *pg_host = "localhost";
    const char *pg_dbname = "votesystem";
    const char *pg_username = "votedb";
    const char *pg_password = "pass1"; // replace with your actual password
    
    // Build the connection parameters array
    const char *const keywords[] = {
        "host", "dbname", "user", "password", NULL
    };
    const char *const values[] = {
        pg_host, pg_dbname, pg_username, pg_password, NULL
    };
    
    // Connect to the PostgreSQL database
    PGconn *conn = PQconnectdbParams(keywords, values, 0);
    
    // Check if the connection was successful
    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Connection to database failed: %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        return 1;
    }

    const char *query;
    if (strcmp(argv[1], "-t") == 0) {
        query = "SELECT ai.title, e.name, e.start_date, \
                        SUM(CASE WHEN aiv.vote = 'Approve' THEN 1 ELSE 0 END) AS approve_count, \
                        SUM(CASE WHEN aiv.vote = 'Deny' THEN 1 ELSE 0 END) AS deny_count, \
                        CASE \
                            WHEN SUM(CASE WHEN aiv.vote = 'Approve' THEN 1 ELSE 0 END) > SUM(CASE WHEN aiv.vote = 'Deny' THEN 1 ELSE 0 END) THEN 'Approved' \
                            ELSE 'Denied' \
                        END as final_verdict \
                 FROM action_item_votes aiv \
                 JOIN action_items ai ON ai.id = aiv.action_item_id \
                 JOIN elections e ON e.id = aiv.election_id \
                 GROUP BY ai.title, e.name, e.start_date;";
    } else if (strcmp(argv[1], "-u") == 0) {
        query = "SELECT v.firstname, v.lastname, ai.title, aiv.vote, e.name, e.start_date \
                          FROM action_item_votes aiv \
                          JOIN voters v ON v.id = aiv.voters_id \
                          JOIN action_items ai ON ai.id = aiv.action_item_id \
                          JOIN elections e ON e.id = aiv.election_id \
                          ORDER BY v.lastname, v.firstname;";
    } else {
        query = "SELECT * FROM action_item_votes;";
    }
    
    // Retrieve data from the database
    PGresult *result = PQexec(conn, query);
    
    // Check for query execution errors
    if (PQresultStatus(result) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Query execution failed: %s\n", PQerrorMessage(conn));
        PQclear(result);
        PQfinish(conn);
        return 1;
    }
    
    int numRows = PQntuples(result);
    if (strcmp(argv[1], "-t") == 0) {
        printf("\"Action Item Title\",\"Election Name\",\"Election Start Date\",\"Approve Count\",\"Deny Count\",\"Final Verdict\"\n");
        for (int i = 0; i < numRows; i++) {
            printf("\"%s\",\"%s\",\"%s\",%s,%s,\"%s\"\n", 
                   PQgetvalue(result, i, 0),
                   PQgetvalue(result, i, 1), 
                   PQgetvalue(result, i, 2), 
                   PQgetvalue(result, i, 3), 
                   PQgetvalue(result, i, 4), 
                   PQgetvalue(result, i, 5));
        }
    } else if (strcmp(argv[1], "-u") == 0) {
        printf("\"Voter First Name\",\"Voter Last Name\",\"Action Item\",\"Vote\",\"Election Name\",\"Election Start Date\"\n");
        for (int i = 0; i < numRows; i++) {
            printf("\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n", 
                   PQgetvalue(result, i, 0),
                   PQgetvalue(result, i, 1),
                   PQgetvalue(result, i, 2),
                   PQgetvalue(result, i, 3),
                   PQgetvalue(result, i, 4),
                   PQgetvalue(result, i, 5));
        }
    } else {
        for (int i = 0; i < numRows; i++) {
            printf("ID: %s\n", PQgetvalue(result, i, 0));
            printf("Election ID: %s\n", PQgetvalue(result, i, 1));
            printf("Voter ID: %s\n", PQgetvalue(result, i, 2));
            printf("Action Item ID: %s\n", PQgetvalue(result, i, 3));
            printf("Vote: %s\n", PQgetvalue(result, i, 4));
            printf("\n");
        }
    }
    
    // Clean up
    PQclear(result);
    PQfinish(conn);
    
    return 0;
}
