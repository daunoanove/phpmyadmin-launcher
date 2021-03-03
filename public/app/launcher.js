let baseUri = '../'

let instances

preparePhpMyAdminConfig()

function url(path){
    return baseUri + path;
}

function preparePhpMyAdminConfig(){
    let settings = {
        'url': url('prepare-phpmyadmin-config'),
        'method': 'POST',
        'timeout': 0,
    };

    $.ajax(settings).done(function(response){
        loadInstances()
    }).fail(function(response){
        fatalError(response.responseJSON.message)
    })
}

function loadInstances(){
    let settings = {
        'url': url('instances'),
        'method': 'GET',
        'timeout': 0,
    };
    
    $.ajax(settings).done(function(response){
        instances = response
        renderData()
    }).fail(function(response){
        fatalError(response.responseJSON.message)
    })
}

function launchInstance(k){
    let settings

    Swal.fire({
        'title': '<h1><i class="fas fa-rocket text-secondary"></i></h1>',
        'html': ' Launch in progress...',
        'icon': '',
        'showConfirmButton': false,
        'allowOutsideClick': false,
    })

    settings = {
        'url': url('check-instance-connection'),
        'data': {
            'k': k
        },
        'method': 'POST',
        'timeout': 0,
    }

    $.ajax(settings)
    .done(function(response){
        settings = {
            'url': url('launch-instance'),
            'data': {
                'k': k
            },
            'method': 'POST',
            'timeout': 0,
        };
        
        $.ajax(settings)
        .done(function(response){
            window.location.replace(response['redirect'])
        })
        .fail(function(response){
            error(response.responseJSON.message)
        })
    })
    .fail(function(response){
        error(response.responseJSON.message)
    })
}

function renderData(){
    let data = {
        'hosts': []
    }

    instances.sort(function(a, b) {
        let userA = a.user.toLowerCase()
        let userB = b.user.toLowerCase()

        if (userA < userB)
            return -1 
        if (userA > userB)
            return 1
        return 0
    });

    let hosts = []
    for ( instanceK in instances ){
        if ( !hosts.includes(instances[instanceK]['host']) ){
            hosts.push(instances[instanceK]['host'])
        }
    }
    hosts.sort()


    for ( hostK in hosts ){
        data['hosts'].push({
            'host': hosts[hostK],
            'instances': [],
        })
    }

    for ( instanceK in instances ){
        for ( hostK in data['hosts'] ){
            if ( instances[instanceK]['host'] === data['hosts'][hostK]['host'] ){
                data['hosts'][hostK]['instances'].push(instances[instanceK])
            }
        }
    }

    let template = document.getElementById('instance_template').innerHTML
    let rendered = Mustache.render(template, data)
    document.getElementById('instances_view').innerHTML = rendered
}

function fatalError(message){
    Swal.fire({
        'title': '',
        'html': message,
        'icon': 'error',
        'showConfirmButton': false,
        'allowOutsideClick': false,
    })
}
function error(message){
    Swal.fire({
        'title': '',
        'html': message,
        'icon': 'error',
        'showConfirmButton': true,
        'allowOutsideClick': true,
    })
}