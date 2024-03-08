################################################################################
#                                                                              #
#                                 skin-tone.py                                 #
#                                                                              #
################################################################################
#                                                                              #
#        This is a script to classify images into racial categories.           #
#                                                                              #
#        Author(s): Abe Megahed                                                #
#                                                                              #
################################################################################
#     Copyright (C) 2024, Data Science Institute, University of Wisconsin      #
################################################################################

import sys
import json
import pathlib
import stone

# get command line arguments
filename = sys.argv[1]

# set options
image_type = 'auto'
palette = None
other_args = []

# process the image
result = stone.process(filename, image_type, palette, *other_args, return_report_image=True)
# show the report image
report_images = result.pop("report_images")  # obtain and remove the report image from the `result`
face_id = 1
# stone.show(report_images[face_id])

# find name of output file
extension = pathlib.Path(filename).suffix
outfilename = filename.replace(extension, ' Skin Tone.json')

# write skin tone analysis to file
outfile = open(outfilename, 'a')
outfile.write(json.dumps(result, indent=4))