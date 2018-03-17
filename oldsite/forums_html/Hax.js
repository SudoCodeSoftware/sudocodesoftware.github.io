var d = new Date();
var time = d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
var username = '';
var AT = '';
var query = getQueryParams(document.location.search);
var ThreadName = query.Name;
var posts = [];
function getQueryParams(qs) {
    qs = qs.split("+").join(" ");

    var params = {}, tokens,
        re = /[?&]?([^=]+)=([^&]*)/g;

    while (tokens = re.exec(qs)) {
        params[decodeURIComponent(tokens[1])]
            = decodeURIComponent(tokens[2]);
    }

    return params;
}

$(document).ready(function() {
    try{
        if (document.cookie.split(" ")[0].split("=")[1].split(';')[0] != undefined && document.cookie.split(" ")[1].split("=")[1].split(';')[0] != undefined){
                if (username != null) {
                    username = document.cookie.split(" ")[0].split("=")[1].split(';')[0];
                    AT = document.cookie.split(" ")[1].split("=")[1].split(';')[0];
                    $.post("cgi-bin/Retrieve.py", {Type: "User_Check", User: username, AT: AT }).done(function( data ) {
                        if (data.trim() == 'True'){
                            $("#add").html('<a onclick="addThread()">+ New Thread</a>');
                            $("#add").html('<a onclick="openreply()">+ Add reply</a>')
                            $("#login").html('<a href="profile.html">' + username + '</a>');
                            $("#username").html('<h1>' + username + '</h1>');
                            $("#usrimg").replaceWith('<img id="usrimg" src="res/usr-img/' + username + '.png" alt="logo" height="150vh" width="150vh">');
                        }
                        else {
                            alert("Illegal Login Token")
                            logout();
                            window.location = 'index.html';
                        }

                    });
                }
        }
    }
    catch(err) {
        console.log("There is no valid username token present")

    }
    
    
});

    $.post("cgi-bin/Retrieve.py", {Type: "Threads", ID: query.ID }).done(function( data ) {
                        $("#name").html(ThreadName)
						for (var i = 0; i < data.split('%').length - 1; i++) {
							$("#threads").append('<tr><td><a href="posts.html?Name='+data.split('%')[i].split(';')[1].split('`')[1]+'&ID='+data.split('%')[i].split(';')[0].split('`')[1]+'"><p>' + data.split('%')[i].split(';')[1].split('`')[1] + "</p></a></td></tr>");
						}
                });

    $.post("cgi-bin/Retrieve.py", {Type: "Boards", id: ""}).done(function( data ) {
                    					
						for (var i = 0; i < data.split('%').length - 1; i++) {
							$("#boards").append('<tr><td><a href="thread.html?Name='+data.split('%')[i].split(';')[1].split('`')[1]+'&ID='+data.split('%')[i].split(';')[0].split('`')[1]+'"><p>' + data.split('%')[i].split(';')[1].split('`')[1] + "</p></a></td></tr>");
						}
                });
 $("#sbox").keypress(function(){
                     search($("#sbox").val())
});    

 function addThread() {
                var threadName = prompt("Enter name of Thread: ");
                $.post("cgi-bin/Save.py", {Type: "Threads", Name: threadName, Parent: query.ID, User: username});
            }
function openreply() {
    $("#add").html('<textarea id="reply" cols="118" rows="10"></textarea><br><input id="submit" type="submit" onclick = "reply();" value="Post" />');
}

function dummy(text) {
    //var time = $(text).parent('tr').children('td').children('p')[0].innerHTML;
    var postData = text[0].value;
    $.post("cgi-bin/Save.py", {Time: time, Content: postData, Type: "UPosts", Parent: query.ID, User: username}).done(function( data ) {
    });
    location.reload();
}
function reply() {
    var postData = $("#reply")[0].value
    $.post("cgi-bin/Save.py", {Time: time  ,Content: postData, Type: "Posts", Parent: query.ID, User: username, AT: AT}).done(function( data ){});
    location.reload();
}

function logout() {
            document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
            document.cookie = "at=; expires=Thu, 01 Jan 1970 00:00:00 UTC";
            }


    $.post("cgi-bin/Retrieve.py", {Type: "Posts", ID: query.ID }).done(function( data ) {

        for (var i = 0; i < data.split('%').length - 1; i++) {
            var Username = data.split('%')[i].split(';')[2].split('`')[1] ;
            var timestamp = data.split('%')[i].split(';')[4].split('`')[1];
            var content = data.split('%')[i].split(';')[3].split('`')[1];
            var id = 'edit'+i   
            var table = 
                '<tr> \
                 <td class="posts3" width="5px"> \
                    <img src="res/usr-img/'+Username+'.png" alt="logo" height="100px" width="100px"> \
                    '+Username+' \
                    <br> \
                    <p>'+timestamp+'</p> \
                    </td> \
                    <td class="posts3" id="'+id+'"> \
                        <p>'+content.replace('\n','<br>')+'</p> \
                        </td>';
            
                if (username == Username) {
                    table +=
                         '<td width="10px"> \
                            &ensp;<input class="edit" id="'+id+'" type="submit" value="Edit"/> \
                            <br> \
                            <br> \
                            <input class="delete" type="submit" id="'+id+'" value="Delete" /> \
                          </td>';
                }

            $("#posts").append(table);

        }
    });

$( ".edit" ).click(function(){
            $("#"+this.id).replaceWith('<textarea id="'+this.id+'" cols="50" rows="5">'+$('#'+this.id).children('p')[0].innerHTML.replace('<br>','\n')+'</textarea><br><input class="UP" id="'+this.id+'" type="submit" value="Post" onclick="dummy('+this.id+');"/>');
        });

                            
$( ".delete" ).click(function(){
            timestamp = $("#"+this.id).parent('tr').children('td').children('p')[0].innerHTML;
            $.post("cgi-bin/Save.py", {Type: "DPosts", User: username, Parent: query.ID , Time: timestamp }).done(function( data ) {
                location.reload();
            });
        });

function search(text) {
    $.post("cgi-bin/Retrieve.py", {Type: "search", Text: text}).done(function( data ) {
        $("#search")[0].innerHTML = '';

         for (var i = 0; i < data.split(String.fromCharCode(30)).length - 1; i++) {
             var returnPost = data.split(String.fromCharCode(30))[i].split(String.fromCharCode(31))[0]
             var returnTimestamp =  data.split(String.fromCharCode(30))[i].split(String.fromCharCode(31))[1]
             var returnUsername = data.split(String.fromCharCode(30))[i].split(String.fromCharCode(31))[2]
             var returnLink = data.split(String.fromCharCode(30))[i].split(String.fromCharCode(31))[3]
             table = " \
             <p>"+i+"</p> \
                <a href='posts.html"+returnLink+"'> \
            <table id='search', bgcolor='#082108' border='2' border-style=solid cellpadding='30' width='80%'> \
                    <tr> \
                          <td class='posts3' width='5px'> \
                              <img src='res/usr-img/"+returnUsername+".png' alt='logo'height='100px' width='100px'> \
                              "+returnUsername+" \
                              <br> \
                              <p>"+returnTimestamp+"</p> \
                          </td> \
                        <td class='posts3'> \
                        <p>"+returnPost+"</p> \
                          </td> \
        </table></a>";
             
        $("#search").append(table);                       
                    
         }
    });
}