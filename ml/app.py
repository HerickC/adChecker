import os
from flask import Flask, request
from threading import Thread
from mlFunctions import *
import json

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

@app.route('/process-image-image', methods=['POST'])
def process_image_image():
    id = request.form['id']
    image = json.loads(request.form['image'])
    Thread(target=processImageImage, args=(id, image)).start()
    return "True"

@app.route('/process-image-title', methods=['POST'])
def process_image_title():
    id = request.form['id']
    data = {
        'title': request.form['title'],
        'image': json.loads(request.form['image'])
    }
    Thread(target=processImageTitle, args=(id, data)).start()
    return "True"

@app.route('/process-image-description', methods=['POST'])
def process_image_description():
    id = request.form['id']
    data = {
        'description': request.form['description'],
        'image': json.loads(request.form['image'])
    }
    Thread(target=processImageDescription, args=(id, data)).start()
    return "True"

@app.route('/process-title-description', methods=['POST'])
def process_title_description():
    id = request.form['id']
    data = {
        'description': request.form['description'],
        'title': request.form['title']
    }
    
    Thread(target=processTitleDescription, args=(id, data)).start()
    return "True"

@app.route('/process-overall', methods=['POST'])
def process_overall():
    id = request.form['id']
    data = {
        'ii': request.form['image'],
        'it': request.form['imgTit'],
        'id': request.form['imgDes'],
        'td': request.form['titDes']
    }
    
    Thread(target=processOverall, args=(id, data)).start()
    return "True"