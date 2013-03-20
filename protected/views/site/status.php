<h2> Flickr Backup <small> status </small> </h2>
<br />

<script>
    var id1 = null;
    var id2 = null;
    var id3 = null;

    $(document).ready(function(){
        
        updateProgressBar1();
        updateProgressBar2();
        updateProgressBar3();
        
        startTimers();
        
        $('#p1-add').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p1&op=add",
                type : "GET",
                cache : false
            })
        });
        $('#p1-del').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p1&op=del",
                type : "GET",
                cache : false
            })
        });
        $('#p1-all').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p1&op=all",
                type : "GET",
                cache : false
            })
        });
        
        $('#p2-add').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p2&op=add",
                type : "GET",
                cache : false
            })
        });
        $('#p2-del').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p2&op=del",
                type : "GET",
                cache : false
            })
        });
        $('#p2-all').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p2&op=all",
                type : "GET",
                cache : false
            })
        });
        
        
        $('#p3-add').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p3&op=add",
                type : "GET",
                cache : false
            })
        });
        $('#p3-del').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p3&op=del",
                type : "GET",
                cache : false
            })
        });
        $('#p3-all').click(function() {
            $.ajax({
                url : "/~tbilou/tbDashboard/index.php?r=site/ManageInstance&type=p3&op=all",
                type : "GET",
                cache : false
            })
        });
        //        $('#doBackup').click(function() {
        //            $.ajax({
        //                url : "/~tbilou/tbDashboard/index.php?r=site/doBackup",
        //                type : "GET",
        //                cache : false
        //            })
        //        });
    });



    function startTimers() {
        id1 = setInterval(updateProgressBar1, 5000);
        id2 = setInterval(updateProgressBar2, 5000);
        id3 = setInterval(updateProgressBar3, 5000);
    }

    function stopTimer(id) {
        clearInterval(id);
    }

    function updateProgressBar1()
    {
        // Make the ajax request to the controller
        $.ajax({
            url : "/~tbilou/tbDashboard/index.php?r=site/getQueueStatus&type=p1",
            type : "GET",
            cache : false,
            success : function(data){
                obj = JSON.parse(data);
                if (obj.percent == 100)
                { 
                    stopTimer(id1);
                    $("#p1").removeClass('progress-warning').addClass('progress-success');
                }
                // Update the progress
                $("#p1 .bar").css('width', obj.percent+'%');
                $('#inst1').html('Workers (' + obj.instances +') <span class="caret"></span>');
                $('#elapsed1').html('<small><small>( '+ obj.elapsed +' )</small></small>');
                $('#counter1').html('<small> [ '+ obj.processed +' | '+ obj.total +' ] </small>');
            }
        });
    }

    function updateProgressBar2()
    {
        // Make the ajax request to the controller
        $.ajax({
            url : "/~tbilou/tbDashboard/index.php?r=site/getQueueStatus&type=p2",
            type : "GET",
            cache : false,
            success : function(data){
                obj = JSON.parse(data);
                if (obj.percent == 100)
                { 
                    stopTimer(id2);
                    $("#p2").removeClass('progress-warning').addClass('progress-success');
                }
                // Update the progress
                $("#p2 .bar").css('width', obj.percent+'%');
                $('#inst2').html('Workers (' + obj.instances +') <span class="caret"></span>');
                $('#elapsed2').html('<small><small>( '+ obj.elapsed +' )</small></small>');
                $('#counter2').html('<small> [ '+ obj.processed +' | '+ obj.total +' ] </small>');

            }
        });
    }

    function updateProgressBar3()
    {
        // Make the ajax request to the controller
        $.ajax({
            url : "/~tbilou/tbDashboard/index.php?r=site/getQueueStatus&type=p3",
            type : "GET",
            cache : false,
            success : function(data){
                obj = JSON.parse(data);
                if (obj.percent == 100)
                { 
                    stopTimer(id3);
                    $("#p3").removeClass('progress-warning').addClass('progress-success');
                }
                // Update the progress
                $("#p3 .bar").css('width', obj.percent+'%');
                $('#inst3').html('Workers (' + obj.instances +') <span class="caret"></span>');
                $('#elapsed3').html('<small><small>( '+ obj.elapsed +' )</small></small>');
                $('#counter3').html('<small> [ '+ obj.processed +' | '+ obj.total +' ] </small>');

            }
        });
    }

</script>


<div class="container">
    <div class="row">
        <div class="span3"><h4>Get Photosets</h4></div>
        <div class="span3 offset4 muted" id="elapsed1" style="margin-top:10px"> ... </div>
    </div>

    <div class="row">
        <div class="span1 ">
            <div class="btn-group">
                <a class="btn-mini dropdown-toggle btn-info" data-toggle="dropdown" href="#" id="inst1">
                    Workers (0)
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li > <a href="#" id="p1-add">Create new Worker</a></li>
                    <li> <a href="#" id="p1-del">Kill Worker</a>  </li>
                    <li class="divider"></li>
                    <li> <a href="#"id="p1-all">Kill all workers</a> </li>
                </ul>
            </div>
        </div>
        <div class="span9"> 
            <div class="progress progress-warning progress-striped" id="p1">
                <div class="bar" style="width: 0%;"></div>
            </div>
        </div>
        <div class="span2 text-info" id="counter1"></div>
    </div>
    
</div>

<hr />

<div class="container">
    <div class="row">
        <div class="span3"> <h4> Get Photos from Photosets </h4> </div>
        <div class="span4 offset4 muted" id="elapsed2" style="margin-top:10px"> ... </div>
    </div>
    <div class="row">
        <div class="span1">
            <div class="btn-group">
                <a class="btn-mini dropdown-toggle btn-info" data-toggle="dropdown" href="#" id="inst2">
                    Workers (0)
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li > <a href="#" id="p2-add">Create new Worker</a></li>
                    <li> <a href="#" id="p2-del">Kill Worker</a>  </li>
                    <li class="divider"></li>
                    <li> <a href="#" id="p2-all">Kill all workers</a> </li>
                </ul>
            </div>
        </div>
        <div class="span9"> 
            <div class="progress progress-warning progress-striped" id="p2">
                <div class="bar" style="width: 0%;"></div>
            </div>
        </div>
        <div class="span2 text-info" id="counter2"></div>
    </div>

</div>

<hr />

<div class="container">
    <div class="row">
        <div class="span3"> <h4>Download Photos </h4> </div>
        <div class="span4 offset4 muted" id="elapsed3" style="margin-top:10px"> ... </div>
    </div>
    <div class="row">
        <div class="span1">
            <div class="btn-group">
                <a class="btn-mini dropdown-toggle btn-info" data-toggle="dropdown" href="#" id="inst3">
                    Workers (0)
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li > <a href="#" id="p3-add">Create new Worker</a></li>
                    <li> <a href="#" id="p3-del">Kill Worker</a>  </li>
                    <li class="divider"></li>
                    <li> <a href="#" id="p3-all">Kill all workers</a> </li>
                </ul>
            </div>
        </div>
        <div class="span9"> 
            <div class="progress progress-warning progress-striped" id="p3">
                <div class="bar" style="width: 0%;"></div>
            </div>
        </div>
        <div class="span2 text-info" id="counter3"></div>
    </div>
</div>
