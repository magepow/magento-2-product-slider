var config = {

	map: {
		'*': {
			'magicproduct'	: "Magiccart_Magicproduct/js/magicproduct",
		},
	},

	// paths: {
	// 	'magiccart/slick'			: 'Magiccart_Magicproduct/js/plugins/slick.min',
	// },

	shim: {
		'magiccart/slick': {
			deps: ['jquery']
		},
		'magicproduct': {
			deps: ['jquery', 'magiccart/slick']
		},

	}
};
