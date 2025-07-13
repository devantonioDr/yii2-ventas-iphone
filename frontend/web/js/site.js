// Imask
var addCMasks = function () {
	// get all elements with class "cedula"
	var elements = document.querySelectorAll('.cedula');

	// loop through each element and apply the mask
	for (var i = 0; i < elements.length; i++) {
		var element = elements[i];
		var digitsMask = window.IMask(element, {
			mask: '000-0000000-0'
		});
	}


	var moneyInputs = document.querySelectorAll('.money_input');

	moneyInputs.forEach(function (element) {
		var moneyMask = IMask(element, {
			mask: Number,
			scale: 0,
			thousandsSeparator: ',',
			padFractionalZeros: false,
			normalizeZeros: false,
			radix: '.',
			mapToRadix: ['.'],
			min: 0
		});
	});


	var digitInputs = document.querySelectorAll('.only_numbers');

	digitInputs.forEach(function (element) {
		var digitsMask = IMask(element, {
			mask: /^\d+$/
		});
	});

};


// Call the function to add the event listeners when the DOM is ready
document.addEventListener('DOMContentLoaded', addCMasks);