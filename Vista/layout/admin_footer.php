      </main>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>

/* ===== SIDEBAR ===== */

(function(){

const btn = document.getElementById('btnToggle');
const overlay = document.getElementById('overlay');

const isDesktop = () => window.innerWidth >= 1024;

if(!btn) return;

btn.addEventListener('click', () => {

if(isDesktop()){
document.body.classList.toggle('sb-rail');
}
else{
document.body.classList.toggle('sb-open');
overlay?.classList.toggle('hidden');
}

});

overlay?.addEventListener('click', () => {

document.body.classList.remove('sb-open');
overlay.classList.add('hidden');

});

window.addEventListener('resize', () => {

if(isDesktop()){

document.body.classList.remove('sb-open');
overlay?.classList.add('hidden');

}else{

document.body.classList.remove('sb-rail');

}

});

})();

/* ===== DATATABLE GLOBAL ===== */

document.addEventListener('DOMContentLoaded', function(){

if(!window.jQuery || !jQuery.fn.DataTable) return;

const tabla = document.getElementById('tblPac');

if(!tabla) return;

const dt = jQuery('#tblPac').DataTable({

pageLength:10,
lengthMenu:[10,25,50,100],

paging:true,
info:true,
ordering:true,
order: [],
searching:true,

autoWidth:false,

dom:'<"top"l>rt<"bottom"ip>',

columnDefs:[
{orderable:false,targets:[7]}
],

language:{
lengthMenu:"Ver _MENU_",
info:"Mostrando _START_ a _END_ de _TOTAL_",
infoEmpty:"Mostrando 0 a 0 de 0",
zeroRecords:"No se encontraron resultados",
emptyTable:"No hay registros",
paginate:{
previous:"‹",
next:"›"
}
}

});

/* conectar buscador superior */

const buscador = document.querySelector(
'input[placeholder="Buscar por N° PAC, OBAC, descripción…"]'
);

if(buscador){

buscador.addEventListener('input', function(e){
dt.search(e.target.value).draw();
});

}

});

</script>

</body>
</html>