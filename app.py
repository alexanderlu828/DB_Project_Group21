from flask import Flask, request, jsonify
import psycopg2
import csv
from io import TextIOWrapper

app = Flask(__name__)

# Database configuration
DATABASE_CONFIG = {
    'host': 'your_postgres_host',
    'database': 'your_database_name',
    'user': 'your_username',
    'password': 'your_password'
}

@app.route('/upload_csv', methods=['POST'])
def upload_csv():
    try:
        # Connect to PostgreSQL
        conn = psycopg2.connect(**DATABASE_CONFIG)
        cursor = conn.cursor()

        # Get the uploaded CSV file from the request
        csv_file = request.files['csvFile']
        csv_reader = csv.reader(TextIOWrapper(csv_file))

        # Begin the transaction
        conn.execute("BEGIN")

        # Insert CSV data into the database
        for row in csv_reader:
            cursor.execute("INSERT INTO csv_data (column1, column2) VALUES (%s, %s)", (row[0], row[1]))
            # Add more columns and placeholders as needed

        # Commit the transaction if all operations are successful
        conn.execute("COMMIT")

        # Close the database connection
        conn.close()

        return jsonify({"status": "success"})

    except Exception as e:
        # Rollback the transaction if any error occurs
        conn.execute("ROLLBACK")
        print(f"Transaction rolled back due to error: {str(e)}")
        return jsonify({"status": "error", "message": str(e)})

if __name__ == '__main__':
    app.run(debug=True)
