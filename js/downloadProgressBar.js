/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


var backupId = null;

function startTimer() {
    backupId = setInterval(updateProgressBar, 1000);
}

function stopTimer() {
    clearInterval(backupId);
}

function updateProgressBar()
{
    // Make the ajax request to the controller
    $.ajax({
        url : "/tbFlickr/index.php?r=ajax/getDownloadStatus",
        type : "GET",
        cache : false,
        success : function(data){ 
            var status = jQuery.parseJSON(data); 
            //var message = "";
            $("#progress").progressbar("value", Math.round(Number(status.photosDownloaded * 100) / status.photosTotal));
            /*if (status.photosDownloaded == status.photosTotal)
                message = "All done ^_^ ";
            else
                message = "Processing ("+status.photosProcessed+"/"+status.photosTotal+") and Downloading "+status.photosDownloaded+"/"+ status.photosTotal+" Photos from a total of "+status.setsTotal+" set(s)";
            
            $('#downloadStatus').text(message);
            $('#downloadProgress').text($("#progress").progressbar("value")+"%");*/
        }
    });
}

