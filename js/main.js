/*******************************************************************************
 * Projekt, Kurs: DT161G
 * File: main.js
 * Desc: main JavaScript file for Projekt
 *
 * Johan Lilja
 * joli1407
 * joli1407@student.miun.se
 ******************************************************************************/

var xhr;                // this XMLHttpRequest object is reused for login/logout
var xhrSession;         // this XMLHttpRequest object is used to examine existing session
var xhrImg;             // this XMLHttpRequest object is used to upload images
var showLoginFeedback = false;  //True if the appropriate place to show login info exists, defined in main()
var loginFormExists = false;

/*******************************************************************************
 * Util functions
 ******************************************************************************/
function byId(id) {
    return document.getElementById(id);
}

function main() {
    //Examine if some elements exist
    if(byId("loginForm"))
        loginFormExists = true;

    if(byId("count"))
        showLoginFeedback = true;

    //Add listeners to existing buttons
    if(byId("loginButton")){
        byId("loginButton").addEventListener("click", doLogin, false);
    }

    if(byId("logoutButton")){
        byId("logoutButton").addEventListener("click", doLogout, false);
    }

    if(byId("imageUploadButton")){
        byId("imageUploadButton").addEventListener("click", doImageUpload, false);
    }

    // Stöd för IE7+, Firefox, Chrome, Opera, Safari
    try {
        if (window.XMLHttpRequest) {
            // code for IE7+, Firefox, Chrome, Opera, Safari
            xhr = new XMLHttpRequest();
            xhrSession = new XMLHttpRequest();
            xhrImg = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            // code for IE6, IE5
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
            xhrSession = new ActiveXObject("Microsoft.XMLHTTP");
            xhrImg = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            throw new Error("Cannot create XMLHttpRequest object");
        }

    } catch (e) {
        alert("XMLHttpRequest failed!" + e.message);
    }

    //Get relevant data for current session(links..)
    doSessionCheck();
}
window.addEventListener("load", main, false); // Connect the main function to window load event


/*******************************************************************************
 * function doImageUpload()
 * Upload image by http (xhr)
 * Does simple type check(extension) and denies non-allowed types
 ******************************************************************************/
function doImageUpload(){
    var type = byId("image").files[0].type;
    if(type == "image/png" || type == "image/jpeg" || type == "image/gif" || type == "image/tiff"){
        xhrImg.addEventListener("readystatechange", processImageUpload, false);
        xhrImg.open("POST", "imageupload.php", true);

        var data = new FormData();
        data.append("image", byId("image").files[0]);
        data.append("category", byId("uploadCategory").value);
        xhrImg.send(data);
    }
    else{
        byId("imageUploadFeedback").innerHTML == "File type not allowed: " + type;
        console.log("File type not allowed: " + type);
        byId("image").value = "";
    }
}

/*******************************************************************************
 * function processImageUpload()
 * When upload is done examine response and display message
 * If an image is sent back(duplicate) display it
 ******************************************************************************/
function processImageUpload(){
    if (xhrImg.readyState === XMLHttpRequest.DONE && xhrImg.status === 200) {
        var myResponse = JSON.parse(this.responseText);
        var feedback = myResponse["message"];

        if(myResponse["image"]){
            feedback += "<br> <img src=\"data:" + myResponse["imageType"] + ";base64," + myResponse["image"] + "\" />";
        }
        byId("imageUploadFeedback").innerHTML = feedback;
    }
}

/*******************************************************************************
 * function doSessionCheck()
 * Requests data for current session
 ******************************************************************************/
function doSessionCheck(){
    xhrSession.addEventListener("readystatechange", processSessionCheck, false);
    xhrSession.open("GET", "sessioncheck.php", true);
    xhrSession.setRequestHeader("Content-Type", "application/json;charset=UTF-8");

    xhrSession.send();
}

/*******************************************************************************
 * function doSessionCheck()
 * Examines response for current session data
 * Outputs menu links based on logged in user role
 * Shows/Hides login/logout form/buttons based on existing login
 ******************************************************************************/
function processSessionCheck(){
    if (xhrSession.readyState === XMLHttpRequest.DONE && xhrSession.status === 200) {
        var myResponse = JSON.parse(this.responseText);

        if(byId("mainLinks")){
            var linkList = byId("mainLinks");
            linkList.innerHTML = "";
            for(var link in myResponse["links"]){
                linkList.innerHTML += "<li>" + "<a href='" + myResponse["links"][link] + "'>" + link + "</a> </li>"
            }
        }

        if(myResponse["loggedIn"]){
            if(showLoginFeedback)
                byId("count").innerHTML = "Welcome " + myResponse["loggedIn"];

            if(loginFormExists){
                byId("logout").style.display = "block";
                byId("login").style.display = "none";
            }
        }
        else if(loginFormExists){
            byId("logout").style.display = "none";
            byId("login").style.display = "block";
        }
    }
}

/*******************************************************************************
 * Function doLogin
 ******************************************************************************/
function doLogin() {
    var arr = {};
    arr["uname"] = byId("uname").value;
    arr["psw"] = byId("psw").value;
    if (arr["uname"] != "" && arr["psw"] != "") {
        var json = JSON.stringify(arr);
        xhr.addEventListener("readystatechange", processLogin, false);
        xhr.open("POST", "login.php", true);
        xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        xhr.send(json);
    }
}

/*******************************************************************************
 * Function doLogout
 ******************************************************************************/
function doLogout() {
    xhr.addEventListener("readystatechange", processLogout, false);
    xhr.open("GET", "logout.php", true);
    xhr.send(null);

}

/*******************************************************************************
 * Function processLogin
 ******************************************************************************/
function processLogin() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        //First we must remove the registered event since we use the same xhr object for login and logout
        xhr.removeEventListener("readystatechange", processLogin, false);
        var myResponse = JSON.parse(this.responseText);

        if(myResponse["result"] == true){
            if(showLoginFeedback)
                byId("count").innerHTML = "Successful login!";
            var linkList = byId("mainLinks");
            linkList.innerHTML = "";
            for(var link in myResponse["links"]){
                linkList.innerHTML += "<li>" + "<a href='" + myResponse["links"][link] + "'>" + link + "</a> </li>"
            }

            byId("logout").style.display = "block";
            byId("login").style.display = "none";
        }
        else{
            if(showLoginFeedback)
                byId("count").innerHTML = myResponse["errorMsg"];
        }

    }
}

/*******************************************************************************
 * Function processLogout
 ******************************************************************************/
function processLogout() {
    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        //First we most remove the registered event since we use the same xhr object for login and logout
        xhr.removeEventListener("readystatechange", processLogout, false);
        var myResponse = JSON.parse(this.responseText);
        if(showLoginFeedback)
            byId("count").innerHTML = myResponse["msg"];

        var linkList = byId("mainLinks");
        linkList.innerHTML = "";
        for(var link in myResponse["links"]){
            linkList.innerHTML += "<li>" + "<a href='" + myResponse["links"][link] + "'>" + link + "</a> </li>"
        }

        byId("login").style.display = "block";
        byId("logout").style.display = "none";

        document.location.reload(true);
    }
}