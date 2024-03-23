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

import os
import sys
import json
import pathlib
import stone
from colormap import rgb2hex, rgb2hls, hls2rgb

# get command line arguments
#
filename = sys.argv[1]

# use "Monk" palette
#

palette = [
	'#f6ede4',
	'#f3e7db',
	'#f7ead0',
	'#eadaba',
	'#d7bd96',
	'#a07e56',
	'#825c43',
	'#604134',
	'#3a312a',
	'#292420'
]

labels = [
	'Monk01',
	'Monk02',
	'Monk03',
	'Monk04',
	'Monk05',
	'Monk06',
	'Monk07',
	'Monk08',
	'Monk09',
	'Monk10'
]

#
# color functions
#

def hex_to_rgb(hex):
	 hex = hex.lstrip('#')
	 hlen = len(hex)
	 return tuple(int(hex[i:i+hlen//3], 16) for i in range(0, hlen, hlen//3))

def adjust_color_lightness(r, g, b, factor):
	h, l, s = rgb2hls(r / 255.0, g / 255.0, b / 255.0)
	l = max(min(l * factor, 1.0), 0.0)
	r, g, b = hls2rgb(h, l, s)
	return rgb2hex(int(r * 255), int(g * 255), int(b * 255))

def lighten(color, factor):
	r, g, b = hex_to_rgb(color)
	return adjust_color_lightness(r, g, b, 1 + factor)

def darken(color, factor):
	r, g, b = hex_to_rgb(color)
	return adjust_color_lightness(r, g, b, 1 - factor)

def contrast(color, factor):
	r, g, b = hex_to_rgb(color)
	r = 127 + (r - 127) * factor;
	g = 127 + (g - 127) * factor;
	b = 127 + (b - 127) * factor;
	return rgb2hex(int(r), int(g), int(b))

#
# main
#

# adjust palette for lighting
#
for i in range(0, len(palette)):
	palette[i] = darken(palette[i], 0.3)

print("palette = ")
print(palette)

# set options
#
image_type = 'color'
other_args = [
	'-l ' + ' '.join(labels)
]

# process the image
#
result = stone.process(filename, 
	image_type = 'color',
	tone_palette = palette,
	tone_labels = labels,
	convert_to_black_white = False,
	n_dominant_colors = 2,
	new_width = 250,
	scale = 1.05,
	min_nbrs = 5,
	min_size = (90, 90),
	threshold = 0.15,
	return_report_image = False
)

# show the report image
#
report_images = result.pop("report_images")  # obtain and remove the report image from the `result`
face_id = 1
# stone.show(report_images[face_id])

# find name of output file
#
extension = pathlib.Path(filename).suffix
outfilename = filename.replace(extension, ' Skin Tone.json')

# write skin tone analysis to file
#
if (os.path.exists(outfilename)):
	os.remove(outfilename);
outfile = open(outfilename, 'a')
outfile.write(json.dumps(result, indent=4))