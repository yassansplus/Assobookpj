const allBtn = document.querySelectorAll('.form-btn button');
const updateStorage = localStorage.getItem('update');

const displayNone = (all = true, formDisplay = null) => {
    document.querySelectorAll('.container form').forEach((form,index) => {
        if(all === true) index > 0 ? form.classList.add('d-none') : '';
        else if(all === false && formDisplay !== null) {
            form.classList.add('d-none');
            formDisplay.classList.remove('d-none');
        } else if(all === false) form.classList.add('d-none');
    });
}

const btnActive = (btn,all = false) => {
    btn.className = 'btn btn-form btn-form-blue';
    if(all === true){
        btn.className = 'btn btn-form btn-form-outline-blue';
    }
}

const createStorage = (id) => {
    localStorage.removeItem('update');
    localStorage.setItem('update', id);
}

const changeForm = () => {
    allBtn.forEach((btn) => {
        btn.addEventListener('click',() => {
            const id = btn.id;
            displayNone(false,document.getElementsByName(id)[0]);
            allBtn.forEach((btnAll) => {
                btnActive(btnAll);
            })
            btnActive(btn,true);
            createStorage(id);
        })
    });
}

if(updateStorage){
    displayNone(false,document.getElementsByName(updateStorage)[0]);
    btnActive(document.querySelector(`.form-btn #${updateStorage}`),true);
    changeForm();
}else{
    displayNone();
    changeForm();
}