import json
from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

mydb = mysql.connector.connect(
  host="localhost",
  user="micropeople",
  passwd="micropeople",
  database="micro_people"
)

@app.route('/')
def hello():
    user_agent = request.headers.get('User-Agent')
    return 'Welcome to the People Service'

@app.route('/all', methods=['GET'])
def check_in():
    dbcursor = mydb.cursor()
    dbcursor.execute("SELECT * FROM people")
    person = dbcursor.fetchall()

    return jsonify(success=True, data=person)

@app.route('/person/<personid>', methods=['GET'])
def person(personid):

    dbcursor = mydb.cursor(prepared=True)
    sql = "SELECT * FROM people where id = %s"
    dbcursor.execute(sql, personid)
    person = dbcursor.fetchall()
    mydb.commit()

    return jsonify(success=True, personid=personid, data=person)

@app.route('/add', methods=['POST'])
def add_person():

    content = request.json
    name = content['name']
    email = content['email']

    dbcursor = mydb.cursor(prepared=True)
    sql = "INSERT INTO people(name, email) VALUES(%s, %s)"
    data = (name, email)
    dbcursor.execute(sql, data)
    #person = dbcursor.fetchall()
    mydb.commit()

    return jsonify(success=True, data=content)

@app.route('/update', methods=['PUT'])
def update_person():

    content = request.json
    id = content['id']
    name = content['name']
    email = content['email']

    dbcursor = mydb.cursor(prepared=True)
    sql = "UPDATE people set name = %s, email = %s WHERE id = %s"
    data = (name, email, id)
    dbcursor.execute(sql, data)
    mydb.commit()

    return jsonify(success=True, data=content)

@app.route('/delete/<id>', methods=['DELETE'])
def delete_person(id):

    dbcursor = mydb.cursor(prepared=True)
    sql = "DELETE FROM people WHERE id = %s"
    dbcursor.execute(sql, id)
    mydb.commit()
    return jsonify(success=True, data=id)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5002)
