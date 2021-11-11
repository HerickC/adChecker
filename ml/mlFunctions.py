from imageai.Detection import ObjectDetection
from googletrans import Translator
import json
import requests
import os
import pickle
import os
import numpy as np
from string import punctuation
import re
import os.path
import urllib.request

filesToDownload = [
    {"name": "word_vectors.p", "link": "https://github.com/HerickC/ML-databases/raw/master/word_vectors.p"},
    {"name": "resnet50_coco_best_v2.1.0.h5", "link": "https://github.com/HerickC/ML-databases/raw/master/resnet50_coco_best_v2.1.0.h5"},
]

def downloadDatabases():
    for file in filesToDownload:
        if not os.path.isfile(file['name']):
            try:
                urllib.request.urlretrieve(file['link'], file['name'])
            except Exception:
                pass

downloadDatabases()

def calcEmbeddingSimilarity(word1, word2):
    downloadDatabases()
    wordVectors = pickle.load( open("word_vectors.p", 'rb'))
    sent1 = re.findall(r"\w+(?:'\w+)?|[^\w\s]", word1)                        
    sent1 = [token.lower() for token in sent1]                                
    sent1 = [re.sub(r"\d+", '', token) for token in sent1]                    
    sent1 = [token for token in sent1 if token not in punctuation]            
    sent1 = [token for token in sent1 if token in wordVectors.vocab] 

    sent2 = re.findall(r"\w+(?:'\w+)?|[^\w\s]", word2)                        
    sent2 = [token.lower() for token in sent2]                                
    sent2 = [re.sub(r"\d+", '', token) for token in sent2]                    
    sent2 = [token for token in sent2 if token not in punctuation]            
    sent2 = [token for token in sent2 if token in wordVectors.vocab] 
  
    similarity = 0

    if (len(sent1) > 0 and len(sent2) > 0):
        similarity = wordVectors.n_similarity(sent1, sent2)
        
    
    return np.float64(similarity)

def calcImageScore(imageData):  
    score = 0
    n = 0

    if (len(imageData) == 1):
        score = imageData[0]['prob'] / 100
        n = 1

    for item1 in imageData:
        for item2 in imageData:
            if item1['name'] != item2['name']:
                trad_item1 = ' '.join(item1['translations'])
                trad_item2 = ' '.join(item2['translations'])

                score += calcEmbeddingSimilarity(trad_item1, trad_item2) * ((item1['prob'] + item2['prob']) / 200)
                n += 1 

    return score/n if (n>0) else 0

def imageAnalyze(image):
    downloadDatabases()
    translator = Translator()
    names = []
    execution_path = os.getcwd()

    detector = ObjectDetection()
    detector.setModelTypeAsRetinaNet()
    detector.setModelPath( os.path.join(execution_path , "resnet50_coco_best_v2.1.0.h5"))
    detector.loadModel()
    detections = detector.detectObjectsFromImage(
        input_image=os.path.join(execution_path , f"upload/{image}"),
        output_image_path=os.path.join(execution_path , f"upload/new_{image}")
    )

    items = []
    for eachObject in detections:
        if (eachObject["name"] in names):
            continue

        names.append(eachObject["name"])
        name = eachObject["name"]
        prob = eachObject["percentage_probability"]
        
        translation = translator.translate(name, dest='pt')
        translation = translation.extra_data['all-translations'][0][1]

        items.append({
            'name': name,
            'prob': prob,
            'translations': translation
        })

    return json.dumps(items)

def calcOverall(data):
    weight = 4
    ii = float(data['ii'])
    it = float(data['it'])
    id = float(data['id'])
    td = float(data['td'])

    return (ii + it + id + td) / weight

def detectObjects(image, id):
    items = imageAnalyze(image)
    data = {'id': id, 'items': items}
    requests.post('http://backend/api/set-image-list', data = data)

def processImageImage(id, data):
    imageScore = calcImageScore(data)
    score = {'id': id, 'process': 'ii', 'score': imageScore}
    requests.post('http://backend/api/process-webhook', data = score)

def processImageTitle(id, data):
    imageData = data['image']
    translations = ' '.join(tuple([translation for image in imageData for translation in image['translations']]))
    scoreIT = calcEmbeddingSimilarity(translations, data['title'])  
    score = {'id': id, 'process': 'it', 'score': scoreIT}
    requests.post('http://backend/api/process-webhook', data = score)

def processImageDescription(id, data):
    imageData = data['image']
    translations = ' '.join(tuple([translation for image in imageData for translation in image['translations']]))
    scoreID = calcEmbeddingSimilarity(translations, data['description'])  
    score = {'id': id, 'process': 'id', 'score': scoreID}
    requests.post('http://backend/api/process-webhook', data = score)

def processTitleDescription(id, data):
    title = data['title']
    description = data['description']
    scoreTD = calcEmbeddingSimilarity(title, description) 
    score = {'id': id, 'process': 'td', 'score': scoreTD}
    requests.post('http://backend/api/process-webhook', data = score)

def processOverall(id, data):
    scoreOV = calcOverall(data)
    score = {'id': id, 'process': 'ov', 'score': scoreOV}
    requests.post('http://backend/api/process-webhook', data = score)
