import json
from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_swagger import swagger
import mysql.connector

app = Flask(__name__)
CORS(app)

mydb = mysql.connector.connect(
  host="localhost",
  user="microall",
  passwd="microall",
  database="micro_places"
)

@app.route('/')
def hello():
    user_agent = request.headers.get('User-Agent')
    return 'Welcome to the places Service'

@app.route('/all', methods=['GET'])
def check_in():
    """
    Return all places
    ---
    tags:
      - places
    definitions:
      - schema:
          id: Place
          properties:
            name:
             type: string
             description: the place's name
    responses:
      201:
        description: Returned all places
    """
    dbcursor = mydb.cursor()
    dbcursor.execute("SELECT * FROM places")
    place = dbcursor.fetchall()

    return jsonify(success=True, data=place)

@app.route('/place/<placeid>', methods=['GET'])
def place(placeid):

    dbcursor = mydb.cursor(prepared=True)
    sql = "SELECT * FROM places where id = %s"
    dbcursor.execute(sql, placeid)
    place = dbcursor.fetchall()
    mydb.commit()

    return jsonify(success=True, placeid=placeid, place=place)

@app.route('/add', methods=['POST'])
def add_place():
    """
    Create a new place
    ---
    tags:
      - places
    definitions:
      - schema:
          id: Place
          properties:
            name:
             type: string
             description: the place's name
    parameters:
      - in: body
        name: body
        schema:
          id: place
          required:
            - name
          properties:
            name:
              type: string
              description: name of place
    responses:
      201:
        description: place created
    """
    content = request.json
    name = content['name']

    dbcursor = mydb.cursor()
    sql = "INSERT INTO places(name) VALUES('" + name + "')"
    dbcursor.execute(sql)
    #place = dbcursor.fetchall()
    mydb.commit()

    return jsonify(success=True, data=content)

@app.route('/update', methods=['PUT'])
def update_place():

    content = request.json
    id = content['id']
    name = content['name']
    email = content['email']

    dbcursor = mydb.cursor(prepared=True)
    sql = "UPDATE places set name = %s, email = %s WHERE id = %s"
    data = (name, email, id)
    dbcursor.execute(sql, data)
    mydb.commit()

    return jsonify(success=True, data=content)

@app.route('/delete/<id>', methods=['DELETE'])
def delete_place(id):

    dbcursor = mydb.cursor(prepared=True)
    sql = "DELETE FROM places WHERE id = %s"
    dbcursor.execute(sql, id)
    mydb.commit()
    return jsonify(success=True, data=id)

@app.route("/spec")
def spec():
    swag = swagger(app)
    swag['info']['version'] = "1.0"
    swag['info']['title'] = "Microservice API for Places"
    return jsonify(swag)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001)
