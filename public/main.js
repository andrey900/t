if( typeof BaseModule != 'function' ){
	function BaseModule(){
	};

	BaseModule.prototype.init = function(){};
}

BaseModule.prototype.init = function(){
	this.updateStatusStadium.start();

	this.selectPlace('.action--selectPlace');
	this.makeOrder('.action--makeOrder');
	this.cancelOrder('.action--cancelOrder');
};

BaseModule.prototype.selectPlace = function(selector){
	var self = this;
	
	this.resultData = function(){
		var ul = document.createElement('ul');
		var $selected = $('.selected');
		if( $selected.length <= 0 ){
			$('.reservation-info').addClass('hide');
			return;
		}
		$('.reservation-info').removeClass('hide');

		var totalSumm = 0;
		$selected.each(function(i, e){
			var $e = $(e);
			var li = document.createElement('li');
			li.innerText = $e.attr('data-uid')
			ul.appendChild(li);
			totalSumm += parseInt($e.attr('data-price'));
		});

		$('.data-result').html('').append(ul);
		$('.total-summ').text(totalSumm);
	};

	$(document).on('click', selector, function(){
		$(this).toggleClass('selected');
		self.resultData();
	});
}

BaseModule.prototype.makeOrder = function(selector){
	var self = this;
	$(document).on('click', selector, function(e){
		e.preventDefault();
		$('.stadium-info').addClass('hide');
		$('.reservation-info').addClass('hide');
		$('.new-order').removeClass('hide');
		self.updateStatusStadium.stop();
	})
}

BaseModule.prototype.cancelOrder = function(selector){
	var self = this;
	$(document).on('click', selector, function(e){
		e.preventDefault();
		$('.stadium-info').removeClass('hide');
		$('.reservation-info').removeClass('hide');
		$('.new-order').addClass('hide');
		self.updateStatusStadium.start();
	});
}

BaseModule.prototype.updateStatusStadium = {
	tmx: false,
	waitAnswer: false,

	start: function(){
		this.tmx = setInterval(this.getStatus.bind(this), 1000);
	},

	stop: function(){
		clearInterval(this.tmx);
	},

	getStatus: function(){
		var self = this;

		if( self.waitAnswer )
			return;

		self.waitAnswer = true;
		$.ajax({
			url: 'ajax.php',
			method: 'get',
			dataType: "json"
		}).done(function(data){
			console.log(data);
			if( data.reserved.length ){
				$.each(data.reserved, function(i, e){
					var item = $('.u-place[data-uid='+e+']');
					if( item.length ){
						if( item.hasClass('selected') ){
							item.click();
						}
						item.addClass('place-reserved');
						item.removeClass('place-free action--selectPlace place-in-proccess');
					}
				});
			}

			if( data.reservation.length ){
				$.each(data.reservation, function(i, e){
					var item = $('.u-place[data-uid='+e+']');
					if( item.length ){
						if( item.hasClass('selected') ){
							item.click();
						}
						item.addClass('place-in-proccess');
						item.removeClass('place-free place-reserved action--selectPlace');
					}
				});
			}

			if( data.free.length ){
				$.each(data.free, function(i, e){
					var item = $('.u-place[data-uid='+e+']');
					if( item.length ){
						item.addClass('place-free action--selectPlace');
						item.removeClass('place-in-proccess place-reserved');
					}
				});
			}
		}).always(function(xhr){
			// self.waitAnswer = false;
		});
	}
}


var loader = new BaseModule;

$(document).ready(loader.init.bind(loader));
