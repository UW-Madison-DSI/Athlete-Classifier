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
import numpy as np
from PIL import Image
import cv2

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
	r = 127 - ((127 - r) * factor);
	g = 127 - ((127 - g) * factor);
	b = 127 - ((127 - b) * factor);
	return rgb2hex(int(r), int(g), int(b))

#
# image manipulation functions
#

def convert_rgba_to_rgb(filename, rgb_filename):
	rgba_image = Image.open(filename)
	rgb_image = rgba_image.convert('RGB')
	rgba_pixels = np.array(rgba_image)
	rgb_pixels = np.array(rgb_image)

	# convert rgb values to white using alpha channel
	#
	if (len(rgba_pixels[0, 0]) > 3):
		for y in range(0, rgb_image.size[0] - 1): 
			for x in range(0, rgb_image.size[1] - 1):
				if rgba_pixels[x, y][3] < 255:    # check alpha
					rgb_pixels[x, y] = (255, 255, 255)

	img = Image.fromarray(rgb_pixels, 'RGB')
	img.save(rgb_filename)

#
# main
#

# adjust palette for lighting
#
# for i in range(0, len(palette)):
#	palette[i] = darken(contrast(palette[i], 0.5), 0.5)
# print("palette = ")
# print(palette)

# set options
#
image_type = 'color'
other_args = [
	'-l ' + ' '.join(labels)
]

# decompose filename
#
tuple = os.path.splitext(filename)
basename = tuple[0]
extension = tuple[1]

# convert image file to rgb (no alpha) version
#
rgb_filename = basename + ' (rgb)' + extension;
convert_rgba_to_rgb(filename, rgb_filename)

# process the image
#
result = stone.process(rgb_filename, 
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
	return_report_image = True
)

# show the report image
#
report_images = result.pop("report_images")  # obtain and remove the report image from the `result`

# save (first) report image
#
keys = report_images.keys();
key = list(keys)[0]
report_image = report_images[key];
report_image = cv2.cvtColor(report_image, cv2.COLOR_BGR2RGB)
pixels = np.array(report_image)
img = Image.fromarray(pixels, 'RGB')
img.save(basename + ' Report.png')

# show report image
#
# stone.show(report_image)

# find name of output file
#
outfilename = basename + ' Skin Tone.json'

# write skin tone analysis to file
#
if (os.path.exists(outfilename)):
	os.remove(outfilename);
outfile = open(outfilename, 'a')
outfile.write(json.dumps(result, indent=4))