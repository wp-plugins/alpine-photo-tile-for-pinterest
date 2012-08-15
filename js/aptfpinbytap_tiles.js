/*
 * Alpine PhotoTile for Pinterest: jQuery Tile Display Functions
 * By: Eric Burger, http://thealpinepress.com
 * Version: 1.0.0
 * Updated: August  2012
 * 
 */

(function( w, s ) {
  s.fn.APTFPINbyTAPDisplayPlugin = function( options ) {
  
    options = s.extend( {}, s.fn.APTFPINbyTAPDisplayPlugin.options, options );
  
    return this.each(function() {  
      var parent = s(this);
      var imageList = s(".APTFPINbyTAP_image_list_class",parent);
      var images = s('.APTFPINbyTAP-image',imageList);
      var allPerms = s('.APTFPINbyTAP-link',imageList);
      var width = parent.width();
      
      var currentRow,img,newDiv,newDivContainer,src,url,height,theClasses,theHeight,theWidth,perm;
      
      if( 'square' == options.shape && 'windows' == options.style ){
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if(i%3 == 0){
            
            theClasses = "APTFPINbyTAP-tile";
            theWidth = (width-8);
            theHeight = theWidth;
            newRow( theHeight );
            addDiv(i);
            
          }else if(i%3 == 1){

            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-first";
            theWidth = (width/2-4-4/2);
            theHeight = theWidth;
            newRow( theHeight );
            addDiv(i);
     
          }else if(i%3 == 2){
        
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-last";
            theWidth = (width/2-4-4/2);
            theHeight = theWidth;
            addDiv(i);
          }
          
          
        });
      }
      else if( 'rectangle' == options.shape && 'windows' == options.style ){
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if(i%3 == 0){
            theWidth = (width-8);
            height = theWidth*img.naturalHeight/img.naturalWidth;
            height = (height?height:width);
            
            newRow(height);
                        
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-tile-rectangle";
            theHeight = (height);

            addDiv(i);
            
          }else if(i%3 == 1){
            theWidth = (width/2-4-4/2);
            height = theWidth*img.naturalHeight/img.naturalWidth;
            height = (height?height:width);
            newRow( height );
            
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-first APTFPINbyTAP-tile-rectangle";
            theHeight = (height);
            theWidth = (width/2-4-4/2);
            addDiv(i);
            
          }else if(i%3 == 2){
            theWidth = (width/2-4-4/2);
            var nextHeight = theWidth*img.naturalHeight/img.naturalWidth;
            nextHeight = (nextHeight?nextHeight:theWidth);
            if(nextHeight && nextHeight<height){
              height = nextHeight;
              updateHeight(newDivContainer,height);
              currentRow.css({'height':height+'px'});
            }
                        
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-last APTFPINbyTAP-tile-rectangle";
            theHeight = (height);
            addDiv(i);
          }

        });
      }      
      else if( 'floor' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        theWidth = (width/options.perRow-4-4/options.perRow);
        theHeight = (width/options.perRow);
          
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if(i%options.perRow == 0){
            newRow(width/options.perRow); 
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-first";            
            addDiv(i);
          }else if(i%options.perRow == (options.perRow -1) ){
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-last";
            addDiv(i);
          }else{    
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile";
            addDiv(i);
          }
        });
      }
      else if( 'wall' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0;
        theHeight = (width/options.perRow);
        
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];

          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm,
            "src":src,
            "img":img
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            
            newRow(theHeight);

            var pos = 0;
            s.each(imageRow,function(j){
              var normalWidth = this.width/sumWidth*width;
              
              img = this.img;  
              url = this.url;
              perm = this.perm;
              src = this.src;
              theClasses = "APTFPINbyTAP-tile";
              theWidth = (normalWidth-4-4/options.perRow);
              addDiv(j);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;
          } 
        });
      }
      else if( 'bookshelf' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0;
        
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm,
            "src":src,
            "img":img
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            
            newRow(10);
            currentRow.addClass('APTFPINbyTAP-bookshelf');
            var pos = 0;
            s.each(imageRow,function(){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
              img = this.img;  
              url = this.url;
              perm = this.perm;
              src = this.src;
              theClasses = "APTFPINbyTAP-book";
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(i);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;maxHeight=0;
          }          
          
        });
      }      
      else if( 'rift' == options.style ){
        parent.css({'width':'100%'});
        width = parent.width();
        var imageRow=[],currentImage,sumWidth=0,maxHeight=0,row=0;
        
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          currentImage = {
            "width":img.naturalWidth,
            "height":img.naturalHeight,
            "url":url,
            "perm":perm,
            "src":src,
            "img":img
          } 
          sumWidth += img.naturalWidth;
          imageRow[imageRow.length] = currentImage;  
          
          if(i%options.perRow == (options.perRow -1) || (images.length-1)==i ){
            if( (images.length-1)==i ){
              sumWidth += (options.perRow - i%options.perRow -1)*imageRow[imageRow.length-1].width;
            }
            newRow(10);
            currentRow.addClass('APTFPINbyTAP-riftline');
            var pos = 0;
            s.each(imageRow,function(){
              var normalWidth = this.width/sumWidth*width;
              var normalHeight = normalWidth*this.height/this.width;
              if( normalHeight > maxHeight ){
                maxHeight = normalHeight;
                currentRow.css({'height':normalHeight+"px"});
              }
              img = this.img;              
              url = this.url;
              perm = this.perm;
              src = this.src;
              theClasses = 'APTFPINbyTAP-rift APTFPINbyTAP-float-'+row;
              theWidth = (normalWidth-4-4/options.perRow);
              theHeight = normalHeight;
              addDiv(i);
              
              newDivContainer.css({
                'left':pos+'px'
              });
              
              pos += normalWidth;
            });
          
            imageRow=[];sumWidth=0;maxHeight=0,row=(row?0:1);
          }          
          
        });
      }   
      else if( 'gallery' == options.style ){
        parent.css({'width':'100%','opacity':0});
        width = parent.width();
        var originalImages = s('img.APTFPINbyTAP-original-image',parent);
        
        var gallery,galleryContainer,galleryHeight;
        theWidth = (width/options.perRow-4-4/options.perRow);
        theHeight = (width/options.perRow);
             
        s.each(images, function(i){
          img = this;
          src = img.src;
          url = 'url("'+src+'")';
          perm = allPerms[i];
          
          if( 0 == i ){
            galleryHeight = width/options.perRow*3;
            
            newRow(galleryHeight); 
                 
            galleryContainer = s('<div class="APTFPINbyTAP-image-div-container APTFPINbyTAP-gallery-container"></div>');
            galleryContainer.css({
              "height":galleryHeight+"px",
              "width":(width-8)+"px",
            });
            
            currentRow.append(galleryContainer);
                             
            if(options.imageBorder){
              galleryContainer.addClass('APTFPINbyTAP-border-div');
              galleryContainer.width( galleryContainer.width()-10 );
              galleryContainer.height( galleryContainer.height()-10 );
            }
            if(options.imageShadow){
              galleryContainer.addClass('APTFPINbyTAP-shadow-div');
            }
            if(options.imageCurve){
              galleryContainer.addClass('APTFPINbyTAP-curve-div');
            }

          }
                    
          if(i%options.perRow == 0){     
            newRow(width/options.perRow); 
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-first";            
            addDiv(i);
          }else if(i%options.perRow == (options.perRow -1) ){           
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile APTFPINbyTAP-half-tile-last";            
            addDiv(i);
          }else{
            theClasses = "APTFPINbyTAP-tile APTFPINbyTAP-half-tile";            
            addDiv(i);
          }
          
          var storeUrl = url;
          if( originalImages[i] ){
            if( originalImages[i].src ){
              storeUrl = 'url("'+originalImages[i].src+'")';
            }
          }

          gallery = s('<div id="'+parent.attr('id')+'-image-'+i+'-gallery" class="APTFPINbyTAP-image-div APTFPINbyTAP-image-gallery"></div>');   
          gallery.css({
            'background-image':storeUrl,
          });
          if( 0 != i ){
            gallery.hide();
          }
          galleryContainer.append(gallery);
          
        });  

        var allThumbs = s('.APTFPINbyTAP-image-div',parent);
        var allGalleries = s('.APTFPINbyTAP-image-gallery',parent);
        s.each(allThumbs,function(){
          var theThumb = s(this);
          if( !theThumb.hasClass('APTFPINbyTAP-image-gallery') ){
            theThumb.hover(function() {
              allGalleries.hide();
              s("#"+theThumb.attr('id')+"-gallery").show();
            }); 
          }
        });
        
        parent.ready(function(){
          parent.css({'opacity':1});
        });
      }

      function newRow(height){
        currentRow = s('<div class="APTFPINbyTAP-row"></div>');
        currentRow.css({'height':height+'px'});
        parent.append(currentRow);
      }
      function addDiv(i){
        newDiv = s('<div id="'+parent.attr('id')+'-image-'+i+'" class="APTFPINbyTAP-image-div"></div>');   
        newDiv.css({
          'background-image':url,
        });
            
        newDivContainer = s('<div class="APTFPINbyTAP-image-div-container '+theClasses+'"></div>');
        newDivContainer.css({
          "height":theHeight+"px",
          "width":theWidth+"px",
        });
        
        currentRow.append(newDivContainer);
        newDivContainer.append(newDiv);
        
        if(perm){
          newDiv.wrap('<a href="'+perm.href+'" class="APTFPINbyTAP-link" target="_blank"></a>');
        }
        if(options.imageBorder){
          newDivContainer.addClass('APTFPINbyTAP-border-div');
          newDivContainer.width( newDivContainer.width()-10 );
          newDivContainer.height( newDivContainer.height()-10 );
        }
        if(options.imageShadow){
          newDivContainer.addClass('APTFPINbyTAP-shadow-div');
        }
        if(options.imageCurve){
          newDivContainer.addClass('APTFPINbyTAP-curve-div');
        }
        if(options.pinIt){
          var media = s(img).attr('data-original');
          media = (media?media:src);
          newDiv.addClass('APTFPINbyTAP-pin-container');
          var link = s('<div class="APTFPINbyTAP-pin-it small"><a href="http://pinterest.com/pin/create/button/?media='+media+'&url='+(options.siteURL)+'" class="pin-it-button" count-layout="horizontal" target="_blank"></a></div>');
          newDiv.append(link);
        }
      }
      
      function updateHeight(aDiv,aHeight){
        aDiv.height(aHeight);
        if(options.imageBorder){
          aDiv.height( aDiv.height()-10 );
        }
      }

    });
  }
  
  s.fn.APTFPINbyTAPDisplayPlugin.options = {
    backgroundClass: 'northbynorth_background',
    parentID: 'parent'
  }    
})( window, jQuery );
  
  
(function( w, s ) {
  s.fn.APTFPINbyTAPAdjustBordersPlugin = function( options ) {
    return this.each(function() {  
      var parent = s(this);
      var images = s('img',parent);

      s.each(images,function(){
        var currentImg = s(this);
        var width = currentImg.parent().width();
        
        // Remove and replace ! important classes
        if( currentImg.hasClass('APTFPINbyTAP-img-border') ){
          width -= 10;
          currentImg.removeClass('APTFPINbyTAP-img-border');
          currentImg.css({
            'max-width':(width)+'px',
            'padding':'5px',
          });
        }else if( currentImg.hasClass('APTFPINbyTAP-img-noborder') ){
          currentImg.removeClass('APTFPINbyTAP-img-noborder');
          currentImg.css({
            'max-width':(width)+'px',
            'padding':'0px',
          });
        }
        
        if( currentImg.hasClass('APTFPINbyTAP-img-shadow') ){
          width -= 8;
          currentImg.removeClass('APTFPINbyTAP-img-shadow');
          currentImg.css({
            "box-shadow": "0 1px 3px rgba(34, 25, 25, 0.4)",
            "margin-left": "4px",
            "margin-right": "4px",
            "margin-bottom": "9px",
            'max-width':(width)+'px',
          });
        }else if( currentImg.hasClass('APTFPINbyTAP-img-noshadow') ){
          currentImg.removeClass('APTFPINbyTAP-img-noshadow');
          currentImg.css({
            'max-width':(width)+'px',
            "box-shadow":"none",
            "margin-left": 0,
            "margin-right": 0
          });
        }
        
      });
    });
  }
    
})( window, jQuery );