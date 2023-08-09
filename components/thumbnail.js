// TODO: rename the file, as common.js or something

function CreateCardForVideo(Metadata)
{
    let VideoSource = document.createElement('source'); 
    VideoSource.type = 'video/mp4';
    VideoSource.src = './videos/' + Metadata.Path;

    let VideoInterface = document.createElement('video');
    VideoInterface.onloadeddata = (event) => {

        let NewAnchor = document.createElement('a');
        NewAnchor.href = 'video.php?id_video=' + Metadata.VideoId;
        NewAnchor.className += "card-title card";

        let canvas = document.createElement('canvas');
        canvas.width = 426;
        canvas.height = 240;
        canvas.getContext("2d").drawImage(event.target, 0, 0, canvas.width, canvas.height);

        let NewTitle = document.createElement('h4');
        NewTitle.innerHTML = Metadata.Titre;

        NewAnchor.appendChild(canvas);
        NewAnchor.appendChild(NewTitle);

        let Card = NewAnchor;

        document.querySelector('.card-container').appendChild(Card);

        // console.log(canvas.toDataURL('image/jpeg'));
        // can pass above to a img element src if you care enough
    };

    VideoInterface.appendChild(VideoSource);
}

function CheckIfUserIsLoggedIn(OnError)
{
    let url = "./controller.php";

    let form_data = new FormData();
        form_data.append('Intention', 'CheckSession');

    const Request = fetch(url, {
            method: "POST",
            mode: "cors",
            cache: "no-cache",
            credentials: "same-origin",
            // headers: { 'Content-Type': 'multipart/form-data' },
            redirect: "follow",
            referrerPolicy: "no-referrer",
            body: form_data
        })
        .then(function (Response) {
            if (!Response.ok)
            {
                if (OnError)
                {
                    OnError();
                }
            }
            // return Response.text();
        })
        // .then(function (ResponseText) {
        //     console.log(ResponseText);
        // })
        ;
}