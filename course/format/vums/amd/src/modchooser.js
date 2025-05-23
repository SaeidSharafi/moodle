define(['jquery', 'core/str', 'core/ajax', 'core/modal_factory', 'core/templates'],
    function($,str, Ajax, ModalFactory, Templates) {
    return {
        init: function(courseId) {
            window.console.log("init modchooser");
            // Add a click event listener to the "Add activity" button
            $('body').on('click', '.add-mod', function(e) {
                e.preventDefault();
                var sectionId = $(this).data('sectionid'); // Get the section ID from the button
                var beforemod = $(this).data('beforemod'); // Get the section ID from the button
                str.get_string('addresourceoractivity').then(function(langString) {
                    ModalFactory.create({
                        type: ModalFactory.types.DEFAULT,
                        title: langString,
                        body: '<div class="modchoosercontainer"  id="activities">' +
                            '<div class="chooser-container">' +
                            '<div class="preloader"></div>' +
                            '</div></div>',
                        footer: '',
                        large: false,
                        templateContext: {
                            classes: 'modchooser vums'
                        }
                    }).done(function (modal) {
                        modal.show();
                        // Make an AJAX request to get the filtered modchooser content
                        Ajax.call([{
                            methodname: 'format_vums_get_filtered_modchooser',
                            args: {
                                sectionid: sectionId,
                                courseid: courseId,
                                beforemod: beforemod,
                            },
                            done: function (response) {
                                Templates.render('format_vums/activitychooser', response).done(function (html) {
                                    let table = document.querySelector('#activities');
                                    Templates.replaceNode(table, html);
                                });
                            },
                            fail: function () {
                                modal.getBody().html('<div>Error loading activities.</div>');
                            }
                        }]);
                    });
                });
            });
        }
    };
});
