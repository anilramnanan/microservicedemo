import json
from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector

app = Flask(__name__)
CORS(app)

mydb = mysql.connector.connect(
  host="localhost",
  user="microall",
  passwd="microall",
  database="micro_items"
)

@app.route('/')
def hello():
    user_agent = request.headers.get('User-Agent')
    return 'Welcome to the items Service'

@app.route('/all', methods=['GET'])
def check_in():
    dbcursor = mydb.cursor()
    dbcursor.execute("SELECT * FROM items")
    item = dbcursor.fetchall()

    return jsonify(success=True, data=item)

@app.route('/item/<itemid>', methods=['GET'])
def item(itemid):

    dbcursor = mydb.cursor(prepared=True)
    sql = "SELECT * FROM items where id = %s"
    dbcursor.execute(sql, itemid)
    item = dbcursor.fetchall()
    mydb.commit()

    return jsonify(success=True, itemid=itemid, item=item)

@app.route('/add', methods=['POST'])
def add_item():

    content = request.json
    name = content['name']
    serialno = content['serialno']
    location = content['location']
    assignedto = content['assignedto']

    dbcursor = mydb.cursor(prepared=True)
    sql = "INSERT INTO items(name, serialno, location, assignedto) VALUES(%s, %s, %s, %s)"
    data = (name, serialno, location, assignedto)
    dbcursor.execute(sql, data)
    mydb.commit()

    return jsonify(success=True, data=content)

@app.route('/delete/<id>', methods=['DELETE'])
def delete_item(id):

    dbcursor = mydb.cursor(prepared=True)
    sql = "DELETE FROM items WHERE id = %s"
    dbcursor.execute(sql, id)
    mydb.commit()
    return jsonify(success=True, data=id)


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5003)
