import sys
import psycopg2
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.platypus import SimpleDocTemplate, Table, TableStyle, Paragraph, Spacer, PageBreak, KeepTogether
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from datetime import datetime
from reportlab.lib.units import inch, mm

DB_CONFIG = {
    "dbname": "votesystem",
    "user": "system",
    "password": "system-pass",
    "host": "localhost",
    "port": "5432"
}

def sanitize_filename(filename):
    invalid_chars = ['<', '>', ':', '"', '/', '\\', '|', '?', '*']
    for char in invalid_chars:
        filename = filename.replace(char, '_')
    return filename

def fetch_voters_data(cur, election_name):
    voters_query = """
    SELECT
        CONCAT(v.firstname, ' ', v.lastname) AS name,
        CASE WHEN COUNT(av.id) = 0 THEN 'No' ELSE 'Yes' END AS has_voted
    FROM voters v
    LEFT JOIN action_item_votes av ON v.id = av.voters_id AND av.election_id = (SELECT id FROM elections WHERE name = %s)
    GROUP BY v.id
    ORDER BY name;
    """
    cur.execute(voters_query, (election_name,))
    return cur.fetchall()

def fetch_detailed_data(cur, election_name):
    detailed_query = """
    SELECT
        ai.title,
        ai.description,
        CONCAT(v.firstname, ' ', v.lastname) AS voter_name,
        aiv.vote
    FROM action_items ai
    JOIN action_item_votes aiv ON ai.id = aiv.action_item_id
    JOIN elections e ON ai.election_id = e.id
    JOIN voters v ON aiv.voters_id = v.id
    WHERE e.name = %s
    ORDER BY ai.title, aiv.vote, v.firstname, v.lastname;
    """
    cur.execute(detailed_query, (election_name,))
    return cur.fetchall()


def fetch_action_items(cur, election_name):
    action_items_query = """
    SELECT
        ai.title,
        COUNT(CASE WHEN aiv.vote = 'Approved' THEN 1 END) AS approve_count,
        COUNT(CASE WHEN aiv.vote = 'Denied' THEN 1 END) AS deny_count,
        e.name AS election_name,
        e.start_date AS election_start_date
    FROM action_items ai
    JOIN action_item_votes aiv ON ai.id = aiv.action_item_id
    JOIN elections e ON ai.election_id = e.id
    WHERE e.name = %s
    GROUP BY ai.title, e.name, e.start_date
    ORDER BY ai.title;
    """
    cur.execute(action_items_query, (election_name,))
    return cur.fetchall()

def fetch_total_voters(cur):
    cur.execute("SELECT COUNT(*) FROM voters;")
    return cur.fetchone()[0]


def main(election_name):
    with psycopg2.connect(**DB_CONFIG) as conn:
        with conn.cursor() as cur:
            results = fetch_action_items(cur, election_name)
            if not results:
                print(f"No data found for election '{election_name}'.")
                sys.exit(1)

            total_voters = fetch_total_voters(cur)
            voters_results = fetch_voters_data(cur, election_name)
            detailed_results = fetch_detailed_data(cur, election_name)

    generate_pdf(election_name, results, total_voters, voters_results, detailed_results)

# [Remaining fetch functions are unchanged, so they're omitted here for brevity.]

    #filename = f"/var/www/html/vote-records/{sanitize_filename(election_name)}.pdf"
def generate_pdf(election_name, results, total_voters, voters_results, detailed_results):
    styles = getSampleStyleSheet()
    header_style = ParagraphStyle("HeaderStyle", parent=styles["Heading1"], fontSize=18, alignment=1, spaceAfter=30, spaceBefore=30, fontName="Courier")
    subtitle_style = ParagraphStyle("SubtitleStyle", parent=styles["Heading2"], fontSize=14, alignment=1, spaceAfter=20, fontName="Courier")
    bold_style = ParagraphStyle("BoldStyle", parent=styles["BodyText"], fontSize=12, leading=15, spaceAfter=10, fontName="Courier")

    election_name, election_date = results[0][-2:]

    headers = ["Action Item Title", "Approve Count", "Deny Count", "Result"]
    data = [headers]
    for title, approve_count, deny_count, _, _ in results:
        result = "Approved" if approve_count > total_voters / 2 else "Denied" if deny_count > total_voters / 2 else "Undetermined"
        title_paragraph = Paragraph(title, styles["BodyText"])  # Convert title to Paragraph
        data.append([title_paragraph, str(approve_count), str(deny_count), result])  # Use title_paragraph

    voters_headers = ["Voter Name", "Has Voted"]
    voters_data = [voters_headers] + voters_results

    sanitized_election_name = sanitize_filename(election_name)
    filename = f"/var/www/html/vote-records/{sanitize_filename(election_name)}.pdf"
    doc = SimpleDocTemplate(filename, pagesize=letter)

    table = create_table(data)
    voters_table = create_table(voters_data)

    flowables = [
        Paragraph(f"Wyoming Energy Co-Op Board of Directors", header_style),
        Spacer(1, 10),
        Paragraph(f"Vote Tally Report: {election_name}", header_style),
        Spacer(1, 10),
        Paragraph("Summary", subtitle_style),
        Paragraph(f"Election Start Date: {election_date}", bold_style),
        Spacer(1, 12),
        KeepTogether(table),  # Ensuring table doesn't get split onto different pages.
        Spacer(1, 24),
        Paragraph("Board Member Roll Call", subtitle_style),
        KeepTogether(voters_table),
        PageBreak()
    ]

    flowables.append(Paragraph("Detailed Tally", subtitle_style))
    current_title = ""
    for title, description, voter_name, vote in detailed_results:
        if title != current_title:
            flowables.extend([
                Spacer(1, 24),
                Paragraph(title, styles["Heading2"]),
                Paragraph(description, styles["BodyText"])
            ])
            current_title = title
        flowables.append(Paragraph(f"{voter_name} voted {vote}", styles["BodyText"]))

    flowables.extend([
        Spacer(1, 24),
        Paragraph(f"PDF Generated on: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}", styles["Normal"])
    ])

    doc.build(flowables, onFirstPage=add_page_number, onLaterPages=add_page_number)
def add_page_number(canvas, doc):
    page_num = canvas.getPageNumber()
    text = f"Page {page_num}"
    canvas.setFont("Courier", 12)
    canvas.drawRightString(200*mm, 20*mm, text)

def create_table(data, column_widths=None):
    if not column_widths:
        column_widths = [3.5*inch, 1.5*inch, 1.5*inch, 1.5*inch]  # Increase width of the first column

    table = Table(data, colWidths=column_widths, repeatRows=1)
    table_style = [
        ('BACKGROUND', (0, 0), (-1, 0), colors.grey),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.whitesmoke),
        ('ALIGN', (0, 0), (-1, -1), 'CENTER'),
        ('FONTNAME', (0, 0), (-1, 0), 'Courier-Bold'),
        ('FONTNAME', (0, 1), (-1, -1), 'Courier'),
        ('BOTTOMPADDING', (0, 0), (-1, 0), 12),
        ('BACKGROUND', (0, 1), (-1, -1), colors.beige),
        ('GRID', (0, 0), (-1, -1), 1, colors.black)
    ]

    for i in range(1, len(data)):
        bg_color = colors.beige if i % 2 == 0 else colors.whitesmoke
        table_style.append(('BACKGROUND', (0, i), (-1, i), bg_color))
        
        # Adjusting row height for titles with length greater than 200 characters.
        if isinstance(data[i][0], Paragraph) and len(data[i][0].text) > 200:
            table_style.append(('FONTSIZE', (0, i), (0, i), 8))
            table_style.append(('VALIGN', (0, i), (0, i), 'TOP'))
            table_style.append(('TOPPADDING', (0, i), (0, i), 5))
            table_style.append(('BOTTOMPADDING', (0, i), (0, i), 5))
            table_style.append(('ROWHEIGHT', (i, i), (i, i), len(data[i][0].text) / 30 * 8 * mm))

    table.setStyle(TableStyle(table_style))
    return table


if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python script_name.py 'Election Name'")
        sys.exit(1)
    
    election_name = sys.argv[1]
    main(election_name)
