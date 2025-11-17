/**
 * Mobile Floating Button
 *  
 * @version 1.0.0
 * @author Andy
 */
( function ($) {
    const fabElement = document.getElementById("floating-snap-btn-wrapper");
    let oldPositionX,
      oldPositionY;

      
    const openItems = ( fabElement ) => {
      var $wrapper = $(fabElement),
          $items   = $wrapper.find('.floating-icons-wrapper').children(),
          count = $items.length, 
          rot   = 270,
          angle = 360 / ((count - 1) * 2),
          distance = 40 + 10 * (count - 2) + (count > 5 ? 10 : 0);
    
      $items.each(function(index, item) {
        $(item).attr(
          'style',
          'transform: rotate(' + rot + 'deg) translate(' + distance + 'px) rotate(' + (- rot) + 'deg); opacity: 1;'
        );
        if( $wrapper.hasClass('right') ) {
          rot = rot - angle;
        } else {
          rot = rot + angle;
        }
      })
    }

    const closeItems = ( fabElement ) => {
      var $wrapper = $(fabElement),
          $items   = $wrapper.find('.floating-icons-wrapper').children();
      
        $items.each(function(index, item) {
          $(item).attr(
            'style',
            'transform: none; opacity: 0;'
          );
        })
    }

    const refreshItems = ( fabElement ) => {
      closeItems(fabElement);
      openItems(fabElement);
    }
    
    const move = (e) => {   
      if (!fabElement.classList.contains("fab-active")) {
        if (e.type === "touchmove") {
          fabElement.style.top = e.touches[0].clientY + "px";
          fabElement.style.left = e.touches[0].clientX + "px";
        } else {
          fabElement.style.top = e.clientY + "px";
          fabElement.style.left = e.clientX + "px";
        }
      }
    };
    
    const mouseDown = (e) => {
      oldPositionY = fabElement.style.top;
      oldPositionX = fabElement.style.left;
      if (e.type === "mousedown") {
        window.addEventListener("mousemove", move);
      } else {
        window.addEventListener("touchmove", move);
      }
    
      fabElement.style.transition = "none";

      e.stopPropagation();
    };
    
    const mouseUp = (e) => {
      if (e.type === "mouseup") {
        window.removeEventListener("mousemove", move);
      } else {
        window.removeEventListener("touchmove", move);
      }
      snapToSide(e);
      fabElement.style.transition = "0.3s ease-in-out left";

      e.stopPropagation();
    };
    
    const snapToSide = (e) => {
      const wrapperElement = document.getElementsByTagName('body')[0];
      const windowWidth = window.innerWidth;
      let currPositionX, currPositionY;
      if (e.type === "touchend") {
        currPositionX = e.changedTouches[0].clientX;
        currPositionY = e.changedTouches[0].clientY;
      } else {
        currPositionX = e.clientX;
        currPositionY = e.clientY;
      }
      if(currPositionY < 50) {
       fabElement.style.top = 50 + "px"; 
      }
      if(currPositionY > wrapperElement.clientHeight - 50) {
        fabElement.style.top = (wrapperElement.clientHeight - 50) + "px"; 
      }
      if (currPositionX < windowWidth / 2) {
        fabElement.style.left = 30 + "px";
        fabElement.classList.remove('right');
        fabElement.classList.add('left');
      } else {
        fabElement.style.left = windowWidth - 30 + "px";
        fabElement.classList.remove('left');
        fabElement.classList.add('right');
      }

      // Rotate Items when its position changed.
      if (fabElement.classList.contains('fab-active')) {
        refreshItems(fabElement);
      }
    };

    if (undefined != fabElement) {
    
      fabElement.addEventListener("mousedown", mouseDown);
      
      fabElement.addEventListener("mouseup", mouseUp);
      
      fabElement.addEventListener("touchstart", mouseDown);
      
      fabElement.addEventListener("touchend", mouseUp);
  
      
      fabElement.addEventListener("click", (e) => {   
          if ( ! fabElement.classList.contains("fab-active") ) {
            openItems(fabElement);
          } else {
            closeItems(fabElement);
          }
  
          if (
              oldPositionY === fabElement.style.top &&
              oldPositionX === fabElement.style.left
          ) {
              fabElement.classList.toggle("fab-active");
          }
  
      });

    }
} ) (jQuery);
