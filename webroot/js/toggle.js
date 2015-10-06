/**
 *  Author: Dave Mariano
 *  Date:   September 29, 2015
 *
 *  This script is to be used with the UploadManager plugin for
 *  CakePHP v3.x, written by Dave Mariano.  It must be included in 
 *  imageManipulation.ctp.
 *
 *  This script will change/toggle how the cursor functions with
 *  the image being manipulated.
 *
 *  There are two modes: crop or resize
 *
 */

var cropButton = $('.crop');
var resizeButton = $('.resize');
var image = $('img.image-man-image');
var imageDiv = document.getElementById('image-man-frame');

if(cropButton.hasClass('active')) selectCrop();
else selectResize();
    
cropButton.click(function () {
    selectCrop();
});

resizeButton.click(function () {
    selectResize();
});

function selectCrop() {
    resizeButton.removeClass('active');
    cropButton.addClass('active');
    imageDiv.style.resize = "none";
    image.imgAreaSelect({
        enable: true,
        handles: true,
    });
}

function selectResize() {
    cropButton.removeClass('active');
    resizeButton.addClass('active');
    imageDiv.style.resize = "both";
    image.imgAreaSelect({
        hide: true,
        disable: true,
    });
}

