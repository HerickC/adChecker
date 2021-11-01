import os
from flask import Flask, request
from threading import Thread
from mlFunctions import detectObjects

app = Flask(__name__)

@app.route('/')
def hello_world():
    return 'AdChecker ML Service'

@app.route('/process-image', methods=['POST'])
def process_image():
    if 'file' not in request.files:
        return "False"
    fileId = request.form['id']
    file = request.files['file']
    path = os.path.join('./upload', file.filename)
    file.save(path)
    Thread(target=detectObjects, args=(file.filename, fileId)).start()
    return fileId
