/* jquery-functions.js
-----------------------

Module "jquery-functions"
Defining functions used for transitions and various animations in scripts on the website.

Dynamica - Jacob Whipp (2022).

*/


function LoginToggle() {
    var state = true;

    $(".ToggleLogReg").click(function(){
        if (state) {
            $("body").animate({backgroundColor: "#1569C7;"}, "slow");
            $(".Motto").animate({color: "white"}, "slow");
            $("#ToggleLogRegText").animate({color: "white"}, "slow");
            
            $("#ToggleLogRegText").text("already signed up?");
            $(".ToggleLogReg").text("log in");

            $(".InputButton").val("Create");

            $("#LoginHeader").text("Register");
            $("#LoginHeader").attr("id", "RegisterHeader");
            
            $("#UsernameBox").attr("placeholder", "Don't use your real name!");
            $("#PasswordBox").attr("placeholder", "Make it secure!");

            $("#LoginForm").attr("action", "account/register.php");

        } else {

            $("body").animate({backgroundColor: "white;"}, "slow");
            $(".Motto").animate({color: "rgb(43, 144, 184);"}, "slow");
            $("#ToggleLogRegText").animate({color: "black"}, "slow");

            $("#ToggleLogRegText").text("no account?");
            $(".ToggleLogReg").text("register");

            $(".InputButton").val("Submit");

            $("#RegisterHeader").text("Login");
            $("#RegisterHeader").attr("id", "LoginHeader");

            $("#UsernameBox").attr("placeholder", "Enter your username");
            $("#PasswordBox").attr("placeholder", "Don't share with anyone!");

            $("#LoginForm").attr("action", "account/login.php");
        }
        state = !state;
    });
}


$(document).ready(function(){ // ensures document loaded before running js
    LoginToggle();
});


/*

    $("#p1").hover(function(){
        alert("You entered p1!");
    },
    function(){
        alert("Bye! You now leave p1!");
    });

*/