var scopes = 'https://www.googleapis.com/auth/analytics.readonly';
var clientId = '6g.apps.googleusercontent.com'; //capsidea
var apiKey = 'AVx4rLsUZKJ'; //capsidea

// This function is called after the Client Library has finished loading
function handleClientLoad() {
  gapi.client.setApiKey(apiKey);
  window.setTimeout(checkAuth,1);
}


function checkAuth() {
  gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: true}, handleAuthResult);
  if(gapi.auth.getToken() == null){
    var authorizeButton = document.getElementById('authorize-button');
    var makeApiCallButton = document.getElementById('make-api-call-button');
    var getProfileId = document.getElementById('getButton');
    document.getElementById('unauth').style.visibility = 'hidden';
    getProfileId.style.display = 'none';
    makeApiCallButton.style.display = 'none';
    authorizeButton.style.visibility = '';
    hide_waiter();
  }
}


function handleAuthResult(authResult) {
  if (authResult) {
    // The user has authorized access
    // Load the Analytics Client. This function is defined in the next section.
    loadAnalyticsClient();
  } else {
    // User has not Authenticated and Authorized
    handleUnAuthorized();
  }
}

// Authorized user
function handleAuthorized() {
  hide_waiter();
  var authorizeButton = document.getElementById('authorize-button');
  var makeApiCallButton = document.getElementById('make-api-call-button');
  var getProfileId = document.getElementById('getButton');
  var unauth = document.getElementById('unauth');

  unauth.style.visibility = '';
  getProfileId.style.display = 'none';
  makeApiCallButton.style.display = 'none';
  authorizeButton.style.visibility = 'hidden';
  document.getElementById('authGoogle').style.visibility = '';
  document.getElementById('bckgrnd').style.visibility = '';
  // makeApiCallButton.onclick = makeApiCall;
  get_account_id(gapi);
}


// Unauthorized user
function handleUnAuthorized() {
  var authorizeButton = document.getElementById('authorize-button');
  var makeApiCallButton = document.getElementById('make-api-call-button');
  var getProfileId = document.getElementById('getButton');
  document.getElementById('unauth').style.visibility = 'hidden';
  getProfileId.style.display = 'none';
  makeApiCallButton.style.display = 'none';
  authorizeButton.style.visibility = '';
  hide_waiter();
}


function handleAuthClick(event) {
  show_waiter();
  gapi.auth.authorize({client_id: clientId, scope: scopes, immediate: false}, handleAuthResult);
  return false;
}


function loadAnalyticsClient() {
  // Load the Analytics client and set handleAuthorized as the callback function
  gapi.client.load('analytics', 'v3', handleAuthorized);
  hide_waiter();
}

function close_dialog() {
  document.getElementById('authGoogle').style.visibility = 'hidden';
  document.getElementById('bckgrnd').style.visibility = 'hidden';
}

function submit_form() {
  var acc_id = $("#account").val();
  var proj_id = $("#webProperty").val();
  var prof_id = {};
  prof_id = $("#profile").val();
  console.log(prof_id);
  if (gapi.auth.getToken() !== null) {

    if (prof_id == undefined || prof_id == "Please select profile...") {
      document.getElementById('authGoogle').style.visibility = '';
      document.getElementById('bckgrnd').style.visibility = '';
    } else {
      document.getElementById('authGoogle').style.visibility = 'hidden';
      document.getElementById('bckgrnd').style.visibility = 'hidden';
      return prof_id;
    }
  } else {
    document.getElementById('authGoogle').style.visibility = 'hidden';
    document.getElementById('bckgrnd').style.visibility = 'hidden';
    alert("You need to authorize in this application.")
  }
}
accountId = '';
webPropertyId ='';
profileId = '';
opt_selected = '';
opt_selected_prop = '';
// oauthToken = gapi.auth.getToken();

function get_ProfileId(opt_selected_prop) {
  var xhr = new XMLHttpRequest();
    var oauthToken = gapi.auth.getToken();
    if (oauthToken == null) {
      alert("You need reauthorize in this application");
    }
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var a = JSON.parse(xhr.responseText);
        delete_options("profile", "Please select profile...");
        for (var i = 0; i < a.items.length; i++) {
          profileId = a.items[i].id;
          var name = a.items[i].name;
          console.log(profileId);
          console.log(name);
          $("#profile_null").after("<option id='"+profileId+"' value='"+profileId+"'>"+name+"</option>");
        };
      }
    }
    xhr.open('GET',
      'https://www.googleapis.com/analytics/v3/management/accounts/' + opt_selected +
      '/webproperties/'+opt_selected_prop+'/profiles?access_token=' + encodeURIComponent(oauthToken.access_token));
    xhr.send();
    $("#profile").change( function () {
      var OkButton = document.getElementById('submit');
      OkButton.style.display = '';
      // submit_form();
    });
}

function catch_choose(select_id) {
  $("#"+select_id).change( function () {
    opt_selected = $("#"+select_id+" option:selected").val();
    console.log(opt_selected);
    return opt_selected;
  });
}

// $("#getButton").after("<select id='account'><option id='account_null'>Not selected</option></select>");
// $("#webProperty").after("<select id='profile'><option id='profile_null'>Not selected</option></select>");

function delete_options(select_id, text) {
  $("#"+select_id).empty();
  $("#"+select_id).html("<option id='"+select_id+"_null'>"+text+"</option>");
}

function get_webProp_Id(opt_selected) {
  var xhr = new XMLHttpRequest();
    var oauthToken = gapi.auth.getToken();
    if (oauthToken == null) {
      alert("You need reauthorize in this application");
    }
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var a = JSON.parse(xhr.responseText);
        delete_options("webProperty","Please select project...");
        delete_options("profile", "Please select profile...");
        for (var i = 0; i < a.items.length; i++) {
          webPropertyId = a.items[i].id;
          var Project = a.items[i].name;
          if(a.items[i].profileCount == 1) {
            console.log(webPropertyId);
            console.log(Project);
            $("#webProperty_null").after("<option id='"+webPropertyId+"' value='"+webPropertyId+"'>"+Project+"</option>");
          }
        };
      }
    }
    xhr.open('GET',
      'https://www.googleapis.com/analytics/v3/management/accounts/' + opt_selected +
      '/webproperties?access_token=' + encodeURIComponent(oauthToken.access_token));
    if(oauthToken == null) {
      alert("Please reload this page.")
    }
    xhr.send();
    $("#webProperty").change( function () {
        opt_selected_prop = $("#webProperty option:selected").val();
        $("#profile").remove();
        $("#webProperty").after("<select id='profile'><option id='profile_null'>Please select profile...</option></select>");
        get_ProfileId(opt_selected_prop);
      });
}

function get_account_id(gapi) {
  $("#account").remove();
  $("#getButton").after("<select id='account'><option id='account_null'>Please select account...</option></select>");
  var xhr = new XMLHttpRequest();
    var oauthToken = gapi.auth.getToken();
    if (oauthToken == null) {
      var authorizeButton = document.getElementById('authorize-button');
      var makeApiCallButton = document.getElementById('make-api-call-button');
      var getProfileId = document.getElementById('getButton');
      document.getElementById('unauth').style.visibility = 'hidden';
      document.getElementById('authGoogle').style.visibility = 'hidden';
      document.getElementById('bckgrnd').style.visibility = 'hidden';
      getProfileId.style.display = 'none';
      makeApiCallButton.style.display = 'none';
      authorizeButton.style.visibility = '';
    }
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        var a = JSON.parse(xhr.responseText);
        delete_options("account", "Please select account...");
        delete_options("webProperty", "Please select project...");
        delete_options("profile", "Please select profile...");
        for (var i = 0; i < a.items.length; i++) {
          accountId = a.items[i].id;
          acc_name = a.items[i].name;
          console.log(acc_name);
          console.log(accountId);
          if (!accountId) {
            alert("Sorry, you have no accounts.")
          };
          $("#account_null").after("<option id='"+accountId+"' value='"+accountId+"'>"+acc_name+"</option>");
        };
      }
    }
    xhr.open('GET',
      'https://www.googleapis.com/analytics/v3/management/accounts' +
      '?access_token=' + encodeURIComponent(oauthToken.access_token));
    xhr.send();
    // catch_choose("account");
      $("#account").change( function () {
        opt_selected = $("#account option:selected").val();
        $("#webProperty").remove();
        $("#profile").remove();
        $("#account").after("<select id='webProperty'><option id='webProperty_null'>Please select project...</option></select>");
        get_webProp_Id(opt_selected);
      });
};

function log_out() {
  var oauthToken = gapi.auth.getToken();
  var revokeUrl = 'https://accounts.google.com/o/oauth2/revoke?token=' + encodeURIComponent(oauthToken.access_token);
  show_waiter();

  $.ajax({
    type: 'GET',
    url: revokeUrl,
    async: false,
    contentType: "application/json",
    dataType: 'jsonp',
    success: function (nullResponse) {
      // Выполните любое действие после отключения пользователя
      document.getElementById('unauth').style.visibility = 'hidden';
      document.getElementById('authorize-button').style.visibility = '';
      $("#webProperty").remove();
      $("#profile").remove();
      delete_options("account", "Please select account...");
      $("#submit").css("display", "none");
      gapi.auth.setToken(null);
      hide_waiter();
      // Ответ всегда неопределенный.
    },
    error: function (e) {

      console.log(e);

      // https://plus.google.com/apps
    }
  });
// Отключение может быть выполнено при нажатии на кнопку
$('#unauth').click(log_out);
}
