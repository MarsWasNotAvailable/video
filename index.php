<?php
    // session_start();
    // var_dump($_SESSION);

    require_once("./components/connexion.php");
    require_once('./components/commons.php');

    // TODO: need to push that to commons.php
    // $DatabaseName = "video";

    $NewConnection = new MaConnexion($DatabaseName, "root", "", "localhost");
    // $NewConnection = new MaConnexion("4354353_video", "4354353_video", "marsdemo1", "fdb28.awardspace.net"); //awardspace
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video: Welcome</title>
    <link rel="icon" href="./images/favicon.ico" type="image/x-icon" >

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <?php include_once './components/navbar.php' ?>
    </header>

    <main>
        <!-- <video width="1280" height="720" controls> -->
        <!-- <video width="480" controls>
            <source src="./videos/butterfly.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>  -->

        <!-- <video id="vid" width="320" height="240" id="video" controls>
            <source src="./videos/lorem.mp4" type=video/mp4>
            Your browser does not support the video tag.
        </video> -->

        <!-- Cette section contient tout les articles -->
        <section class="card-container">
            <!-- <?php
                // <p class="resume">' . $Each['resume'] . '</p>



                $AllVisibleArticles = $NewConnection->select_recent_videos();
                foreach($AllVisibleArticles as $Each)
                {    
                    echo
                    '<div class="card">
                        <div>

                            <canvas id="' . $Each['id_video'] . '" class="card-image" >Thumbnail</canvas>
                        </div>
                        <div class= "card-text">
                            <a href="video.php?id_video=' . $Each['id_video'] . '" class="card-title"><h3>' . $Each['titre'] . '</h3></a>
                            <p class="date">' . $Each['date'] . '</p>
                        </div>
                    </div>';
                }
            ?> -->
        </section> 
    </main>

    <script defer>

        var VideoSource = document.createElement("source"); 
        VideoSource.type = "video/mp4";
        VideoSource.src = './videos/lorem.mp4';

        let VideoInterface = document.createElement("video");
        VideoInterface.onloadeddata = (event) => {
            let canvas = document.createElement("canvas");

            canvas.width = 1280;
            canvas.height = 720;

            canvas.getContext("2d").drawImage(event.target, 0, 0, canvas.width, canvas.height);

            document.querySelector('.card-container').appendChild(canvas);

            console.log('yay');
        };

        VideoInterface.appendChild(VideoSource);





        // let video = document.createElement('video');
        // video.src = "./videos/lorem.mp4";

        // // var canvas = document.getElementById('canvas');
        // var canvas = document.getElementsByTagName('canvas')[0];
        
        // canvas.getContext('2d').drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

        // const offscreen = new OffscreenCanvas(256, 256);
        // const gl = offscreen.getContext("webgl");
        // // gl.drawImage();
        // let c = document.createElement('canvas');
        // gl.drawImage(c);
        


        // window.addEventListener('focus', ()=>{
        //     // We can make the page check if the page session has expired here

        //     let url = "./controller.php";

        //     let form_data = new FormData();
        //         form_data.append('Intention', 'CheckSession');

        //     const Request = fetch(url, {
        //             method: "POST",
        //             mode: "cors",
        //             cache: "no-cache",
        //             credentials: "same-origin",
        //             // headers: { 'Content-Type': 'multipart/form-data' },
        //             redirect: "follow",
        //             referrerPolicy: "no-referrer",
        //             body: form_data
        //         })
        //         .then(function (Response) {
        //             if (!Response.ok)
        //             {
        //                 window.location = 'login.php';
        //             }
        //             // return Response.text();
        //         })
        //         // .then(function (ResponseText) {
        //         //     console.log(ResponseText);
        //         // })
        //         ;
        // });
    </script>

<footer>
    <?php include_once './components/footer.php' ?>
</footer>

</body>

</html>
