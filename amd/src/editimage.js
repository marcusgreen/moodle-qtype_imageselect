define(['qtype_imageselect/cropper'], function(Cropper) {
  return {
      init: function(pic) {
        debugger;

        image = document.getElementById("mgpic");
        var C = new Cropper(image,{
          viewMode: 3,
          dragMode: 'move',
          autoCropArea: 1,
          restore: false,
          modal: false,
          guides: true,
          highlight: true,
          cropBoxMovable: true,
          cropBoxResizable: true,
          toggleDragModeOnDblclick: true,


        ready() {
          alert('ready');
          // this.cropper[method](argument1, , argument2, ..., argumentN);
         // this.cropper.move(1, -1);
         // this.setData('width:10px');

          // Allows chain composition
         // this.cropper.move(1, -1).rotate(45).scale(1, -1);
         // this.cropper.scale(-10,-10);
        }
      },

        );

      }
    }
});