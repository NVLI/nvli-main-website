//Options to load in Open Video Annotation, for all the plugins
(function($){
var options = {
        optionsAnnotator: {
            permissions: {},
            //auth: {tokenUrl:'http://catch.aws.af.cm/annotator/token'},
            store: {
                // The endpoint of the store on your server.
                //prefix: 'http://afstore.aws.af.cm/annotator',
                prefix: window.location.protocol + "//" + window.location.host + '/annotations/apis',

                annotationData: {
                    uri: 'http://danielcebrian.com/annotations/demo.html'
                },
                loadFromSearch: {
                    limit: 10000,
                    uri: 'http://danielcebrian.com/annotations/demo.html'
                }
            },
            richText: {
                tinymce: {
                    selector: "li.annotator-item textarea",
                    plugins: "media image insertdatetime link code",
                    menubar: false,
                    toolbar_items_size: 'small',
                    extended_valid_elements: "iframe[src|frameborder|style|scrolling|class|width|height|name|align|id]",
                    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media rubric | code "
                }
            },
            share: {}, //Share plugin
            annotator: {} //Annotator core
        },
        optionsVideoJS: {
            techOrder: ["html5", "flash", "youtube"]
        },
        optionsRS: {},
        optionsOVA: {
            posBigNew: 'none' /*,NumAnnotations:20*/
        }
}
    //Load the plugin Open Video Annotation
var ova = new OpenVideoAnnotation.Annotator($('#airlock'), options);
//change the user (Experimental)
ova.setCurrentUser($('#username').val());
$('#username').change(function() {
    ova.setCurrentUser($(this).val());
});
})(jQuery);