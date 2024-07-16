require('./bootstrap');
import swal from 'sweetalert2';
window.Swal = swal;
window.moment = require('moment');

//call page js


require('./callpage/callpage');
require('./call-page-layout/call_page_layout');
require('./display-page/display-page');
require('./installer/installer.js');

// $(document).ready(function() {
	
// 	setTimeout(function(){
		
// 		// $('h1').css('color','#222222');
// 	}, 3000);
	
// });