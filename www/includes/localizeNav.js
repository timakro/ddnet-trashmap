var localizeNav = (function (public) {
	public = {};

	public.initialize = function() {
		// instanciate new modal
		localityTab = document.querySelector(".locality_tab");
		localityTab.onclick = function(event) {
			var modal = new tingle.modal({
			    closeMethods: ['overlay', 'button', 'escape'],
			    closeLabel: "Close",
			    cssClass: ['custom-class-1', 'custom-class-2'],
			    onOpen: function() {
			        console.log('modal open');
			    },
			    onClose: function() {
			        console.log('modal closed');
			    },
			    beforeClose: function() {
			        // here's goes some logic
			        // e.g. save content before closing the modal
			        return true; // close the modal
			    	return false; // nothing happens
			    }
			});
			modal.setContent(document.querySelector(".locality_modal").innerHTML);
			modal.open();
		}
	};
	return public;
}(localizeNav || {}))