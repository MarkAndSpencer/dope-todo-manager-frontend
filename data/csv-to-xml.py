#!/usr/bin/python3

import xml.etree.ElementTree as ET
import xml.dom.minidom as minidom
import csv

tasks = ET.Element('tasks')
root = ET.ElementTree(tasks)

columns = ['id', 'task', 'priority', 'size', 'group', 'deadline', 'status', 'flag']

with open('tasks.csv', newline='') as csvFile:
    taskReader = csv.reader(csvFile, delimiter=',', quotechar='"')
    for row in taskReader:
        if row[0].isnumeric():
            task = ET.SubElement(tasks, 'task')
            for i, col in enumerate(columns):
                newElement = ET.SubElement(task, col)
                newElement.text = row[i]
        
rough = ET.tostring(tasks, 'utf-8')
reparsed = minidom.parseString(rough)

with open('tasks.xml', 'w') as xmlFile:
    xmlFile.write(reparsed.toprettyxml(indent="\t"))
