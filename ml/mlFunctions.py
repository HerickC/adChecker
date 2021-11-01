from imageai.Detection import ObjectDetection
import json
import requests
import os

def detectObjects(image, id):
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
        items.append(eachObject["name"])

    items = json.dumps(items)

    data = {'id': id, 'items': items}
    requests.post('http://backend/api/set-image-list', data = data)