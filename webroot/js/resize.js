/**
 *  Author: Dave Mariano
 *  Date:   September 17, 2015
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

// Image save
var saveButton = document.getElementById('save-man');
// Image cancel
var cancelButton = document.getElementById('cancel-man');
// Form to be submitted
var resizeForm = document.getElementById('resize-form');

var curCssHeight = Number(image.height);
var curCssWidth = Number(image.width);

/**
 * The sizes in the height and width fields respond to the
 * mouse moving only when the user is resizing the frame
 * via holding mouse down and dragging.
 */
imageDiv.onmousedown = function () {
    imageDiv.onmousemove = function() { changeValues(); };  
};

/**
 *  Once the user lets go of the frame, the event for
 *  the mouse moving over the frame should be reset to
 *  nothing, the save button should be disabled so the
 *  user cannot press it during the resize operation,
 *  and the newly calculated values will be submitted.
 */
imageDiv.onmouseup = function () {
    imageDiv.onmousemove = function() { /*nothing*/ };
    saveButton.disabled = true;
    resizeForm.submit();
}

/**
 * When not submitting the resize form when the user
 * releases the mouse, double clicking the frame will
 * reset the div back to its original size so the new
 * size has to be calculated
 */
/*imageDiv.ondblclick(function(){
    // Wait .2 seconds for the div to reset its size 
    setTimeout(function(){changeValues();}, 200);
});*/

/**
 * Change the values of the height and width fields for the resize
 * action before submiting the values for manipulation.
 *
 * The width and height variables are CSS width and height, not
 * actual image width and height.  The actual image width and height
 * are stored in their respective fields in the view.  We need to
 * keep things to scale, so percentage of change is to be used.
 */
function changeValues() {
    var widthField = $('input#width');
    var heightField = $('input#height');
    var newCssWidth = Math.round(Number(image.width));
    var newCssHeight = Math.round(Number(image.height));
    
    // Multiply the text field values with divided values
    if(newCssWidth != curCssWidth) {
        var widthChange = newCssWidth / curCssWidth;
        var newFieldVal = Number(widthField.val()) * widthChange;
        widthField.val(Math.round(newFieldVal));
        curCssWidth = newCssWidth;
    }
    
    if(newCssHeight != curCssHeight) {
        var heightChange = newCssHeight / curCssHeight;
        var newFieldVal = Number(heightField.val()) * heightChange;
        heightField.val(Math.round(newFieldVal));
        curCssHeight = newCssHeight;
    }
}
