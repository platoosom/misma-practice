function getAndGenerateAppointmentList(){

    var urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('date')){
        var params = "date="+urlParams.get('date')
    }else{
        var params = ""
    }

    var http = new XMLHttpRequest()
    var url = "main.php?"+params

    http.open("GET", url, true)

    http.onreadystatechange = function() {
        if(http.readyState == 4 && http.status == 200) {
            var data = JSON.parse(http.responseText)

            // Show start - end date
            document.getElementById('start').innerHTML = formatDate(new Date(data.range.start))
            document.getElementById('end').innerHTML = formatDate(new Date(data.range.end))

            // Clear table
            var table = document.getElementById('table-appointment')
            while(table.rows.length > 1) {
                table.deleteRow(1)
            }

            // Clear dropdown list
            var select = document.getElementById('date')
            while (select.firstChild) {
                select.removeChild(select.firstChild)
            }

            for(appointment in data.calendar){

                // Add new data to table
                var row = table.insertRow(table.rows.length)
                var col1 = row.insertCell(0)
                    col1.innerHTML = appointment
                    col1.className = 'cell-date'
                var col2 = row.insertCell(1)
                    col2.innerHTML = (data.calendar[appointment] == null)? '': data.calendar[appointment].name+', '+data.calendar[appointment].email
                    col2.className = 'cell-appointment'

                // Add dropdown list item
                if(data.calendar[appointment] == null){
                    select.options[select.options.length] = new Option(appointment, appointment)
                }
            }
        }
    }

    http.send()
}


function saveAppointment() {
    var spin = document.getElementById('spin')

    var date = document.getElementById("date").value
    var name = document.getElementById("name").value
    var email = document.getElementById("email").value

    var http = new XMLHttpRequest()
    var url = "main.php"
    var params = "date="+date+"&name="+name+"&email="+email

    http.open("POST", url, true)

    //Send the proper header information along with the request
    http.setRequestHeader("Content-type", "application/x-www-form-urlencoded")

    http.onreadystatechange = function() {
        if(http.readyState == 4 && http.status == 200) {
            var data = JSON.parse(http.responseText)
            document.getElementById("message").innerHTML = "<span class='green'>"+data.message+"</span>"
            clearForm()
            getAndGenerateAppointmentList()
        }

        spin.setAttribute('class', 'hide')
        enableForm()
    }

    http.send(params)
    spin.removeAttribute('class')
    disableForm()
}

function clearForm() {
    document.getElementById("name").value = ""
    document.getElementById("email").value = ""
}

function formatDate(date) {
    var monthNames = [
        "Jan", "Feb", "Mar",
        "Apr", "May", "Jun", "Jul",
        "Aug", "Sep", "Oct",
        "Nov", "Dec"
    ]

    var day = date.getDate()
    var monthIndex = date.getMonth()
    var year = date.getFullYear()

    return monthNames[monthIndex] + ' ' + day + ', ' + year
}

function disableForm() {
    document.getElementById("date").setAttribute('disabled', 'disabled')
    document.getElementById("name").setAttribute('disabled', 'disabled')
    document.getElementById("email").setAttribute('disabled', 'disabled')
    document.getElementById("submit").setAttribute('disabled', 'disabled')
}

function enableForm() {
    document.getElementById("date").removeAttribute('disabled')
    document.getElementById("name").removeAttribute('disabled')
    document.getElementById("email").removeAttribute('disabled')
    document.getElementById("submit").removeAttribute('disabled')
}

getAndGenerateAppointmentList()
