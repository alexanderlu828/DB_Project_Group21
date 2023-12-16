from flask import Flask, render_template, request, jsonify
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

def is_location_name_duplicate(cursor, location_name):
    # Check if a location with the same name already exists in the database
    cursor.execute("SELECT COUNT(*) FROM VACCINATION_LOCATION WHERE Location_name = %s", (location_name,))
    return cursor.fetchone()[0] > 0

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/upload_csv', methods=['POST'])
def upload_csv():
    try:
        # Connect to PostgreSQL
        conn = psycopg2.connect(**DATABASE_CONFIG)
        cursor = conn.cursor()

        # Get the uploaded CSV file
        csv_file = request.files['csvFile']
        csv_reader = csv.reader(TextIOWrapper(csv_file))


        # Insert CSV data into the database
        for row in csv_reader:
            location_id, location_name, location_address, slot_capacity, service_start_time, service_end_time = row

            # Check if the location name already exists
            if is_location_name_duplicate(cursor, location_name):
                conn.rollback()
                raise ValueError(f"Location with name '{location_name}' already exists in the database")

            cursor.execute(
                "INSERT INTO VACCINATION_LOCATION (Location_id, Location_name, Location_address, Slot_capacity, Service_start_time, Service_end_time) "
                "VALUES (%s, %s, %s, %s, %s, %s)",
                (location_id, location_name, location_address, int(slot_capacity), service_start_time, service_end_time)
            )

        # Commit the transaction if all operations are successful
        conn.commit()

        # Close the database connection
        conn.close()

        return jsonify({"status": "success"})

    except Exception as e:
        # Rollback the transaction if any error occurs
        conn.rollback()
        print(f"Transaction rolled back due to error: {str(e)}")
        return jsonify({"status": "error", "message": str(e)})

if __name__ == '__main__':
    app.run(debug=True)
