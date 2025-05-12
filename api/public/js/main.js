

function showAlert(type, title, position = 'center', duration = 2000) {
    const Toast = Swal.mixin({
        toast: true,
        position: position,
        showConfirmButton: false,
        timer: duration,
        // timerProgressBar: true,
        // customClass: {
        //     container: 'custom-toast ' + type,
        //     popup: 'custom-toast ' + type,
        //     title: 'custom-toast ' + type
        // },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: title,
        // customClass: {
        //     icon: 'custom-toast-icon'
        // }
    });
}




function showAlertWithCompletion(type, title, completion) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'center',
        showConfirmButton: false,
        timer: 2000,
        // timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    Toast.fire({
        icon: type,
        title: title
    }).then(completion);
}


// function populateSelectFromDsId(c_selector, dataSource) {
//     let select = document.getElementById(c_selector);
//     for (let i = 0; i < dataSource.length; i++) {
//         var opt = dataSource[i]['text'];
//         let el = document.createElement("option");
//         el.textContent = opt;
//         el.value = dataSource[i]['value'];
//         select.appendChild(el);
//     }
//
// }


function populateSelectFromDs(dataId, dataSource) {
    let select = document.querySelector('[data-id="' + dataId + '"]');
    if (!select) {
        console.error("Element with data-id '" + dataId + "' not found.");
        return;
    }

    // Clear existing options
    select.innerHTML = '';

    for (let i = 0; i < dataSource.length; i++) {
        let opt = dataSource[i]['text'];
        let el = document.createElement("option");
        el.textContent = opt;
        el.value = dataSource[i]['value'];
        select.appendChild(el);
    }
}

