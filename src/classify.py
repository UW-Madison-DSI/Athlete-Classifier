################################################################################
#                                                                              #
#                                 classify.py                                  #
#                                                                              #
################################################################################
#                                                                              #
#        This is a script to classify images into racial categories.           #
#                                                                              #
#        Author(s): Abe Megahed                                                #
#                                                                              #
################################################################################
#     Copyright (C) 2023, Data Science Institute, University of Wisconsin      #
################################################################################

from deepface import DeepFace
import sys
import json
import pathlib

# get command line arguments
filename = sys.argv[1]

# classify image
attributes = ['race']
demography = DeepFace.analyze(filename, attributes, enforce_detection=False)
print(demography)

# find name of output file
extension = pathlib.Path(filename).suffix
outfilename = filename.replace(extension, ' Demography.json')

# write demography to file
outfile = open(outfilename, 'a')
outfile.write(json.dumps(demography, indent=4))