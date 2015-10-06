/**
 *  Author: Dave Mariano
 *  Date:   September 29, 2015
 *
 *  This script is to be used with the UploadManager plugin for
 *  CakePHP v3.x, written by Dave Mariano.  It must be included in 
 *  imageManipulation.ctp.
 *
 *  When the page loads, initial values of actual image width and 
 *  height as well as CSS width and height are recorded.  When the 
 *  user stretches/skews the corners of an image, changing its size, 
 *  the fields associated with the resize action change change as well.
 *
 *  When the user let's go of the mouse, the temporary image is updated.
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

