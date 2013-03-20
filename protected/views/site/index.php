
<form action="<?php echo $this->createUrl('site/doBackup') ?>" method="post">
    <div class = "hero-unit">

        <h1><img src="<?php echo Yii::app()->baseUrl ?>/images/flickr-logo.png" width="50" height="50" style="margin-bottom: 15px"> Flickr Dashboard</h1>
        <p> GUI to monitor you flickr backup </p>

        <ul><li>Monitor Download Progress</li>
            <li>Stop and Start Workers</li>
        </ul>
        <br />
        <p>
            <button type="submit" class="btn btn-primary btn-large" name="backup">Start Backup</button>
            <button type="submit" class="btn btn-large" name="status">View Backup</button>
        </p>
    </div>

    <form>
        <fieldset>
            <div class="accordion" id="accordion2">

                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">Workers Config</a>
                    </div>
                    <div id="collapseOne" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <label class="label label-info">Photosets.GetList Workers</label>
                            <input type="text" placeholder="1" class="span1" name="c1" value="1">
                            <span class="help-inline"><small>The number of workers to fetch the photosets List</small></span>


                            <label class="label label-info">Photosets.GetPhotos Workers</label>
                            <input type="text" placeholder="10" class="span1" name="c2" value="5">
                            <span class="help-inline"><small>The number of workers to Process the Photosets and get the Photos</small></span>

                            <label class="label label-info">Photosets.GetPhotos Workers</label>
                            <input type="text" placeholder="20" class="span1" name="c3" value="5">
                            <span class="help-inline"><small>The workers to download the Photos</small></span>

                            <label class="label label-important">Path to the workers</label>
                            <input type="text" class="span4" name="workerspath" value="/home/tbilou/Documents/git/tbFlickr/php/">
                            <span class="help-inline"><small>The path where the workers are located</small></span>

                            <label class="label label-default">Download Path</label>
                            <input type="text" placeholder="/home/tbilou/Pictures/" class="span4" name="cpath" value="/home/tbilou/Pictures/">
                            <span class="help-inline"><small>The path where the photos will be downloaded to</small></span>

                            <label class="label label-default">Log output Path</label>
                            <input type="text" placeholder="/var/log/klogger" class="span4" name="clog" value="/var/log/klogger">
                            <span class="help-inline"><small>The path where the logs are stored</small></span>
                        </div>
                    </div>
                </div>

                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">Downloads Config</a>
                    </div>
                    <div id="collapseTwo" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <label class="label label-default">Download Path</label>
                            <input type="text" placeholder="/home/tbilou/Pictures/" class="span4" name="cpath" value="/home/tbilou/Pictures/">
                            <span class="help-inline"><small>The path where the photos will be downloaded to</small></span>


                        </div>

                    </div>
                </div>


                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">Redis Config</a>
                    </div>
                    <div id="collapseThree" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <label class="label label-default">Redis Host</label>
                            <input type="text" placeholder="127.0.0.1" class="span4" name="redis-server" value="127.0.0.1">
                            <span class="help-inline"><small>The host name/ip of the redis server</small></span>

                            <label class="label label-default">Redis Port</label>
                            <input type="text" placeholder="6379" class="span4" name="redis-port" value="6379">
                            <span class="help-inline"><small>The port where the redis server is listening on</small></span>
                        </div>

                    </div>
                </div>

                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">Cache Config</a>
                    </div>
                    <div id="collapseFour" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <label class="label label-default">Cache Type</label>
                            <select name="cache-type">
                                <option>FileSystem</option>
                                <option>Redis</option>
                                <option>No Cache</option>
                            </select>
                            <span class="help-inline"><small>What Type of Cache to use</small></span>

                            <label class="label label-default">Cache Time to live (seconds)</label>
                            <input type="text" placeholder="600" class="span4" name="cache-ttl" value="600">
                            <span class="help-inline"><small>How long should the cache be valid for</small></span>
                        </div>

                    </div>
                </div>



            </div>





        </fieldset>
    </form>