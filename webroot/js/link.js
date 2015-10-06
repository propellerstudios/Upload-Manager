document.getElementById('resize-link-btn').onclick =
    function() { resizeLink(); };

document.getElementById('crop-link-btn').onclick =
    function() { cropLink(); };

function resizeLink() {
    var resizeLinkIcon = $('.resize-link');
    
    if(resizeLinkIcon.hasClass('link')) {
        resizeLinkIcon.removeClass('link');
        resizeLinkIcon.addClass('unlink');
        
        resizeLinkIcon.removeClass('fa-link');
        resizeLinkIcon.addClass('fa-chain-broken');
    }
    else if(resizeLinkIcon.hasClass('unlink')) {
        resizeLinkIcon.removeClass('unlink');
        resizeLinkIcon.addClass('link');
        
        resizeLinkIcon.removeClass('fa-chain-broken');
        resizeLinkIcon.addClass('fa-link');
    }
}

function cropLink() {
    var cropLinkIcon = $('.crop-link');
}