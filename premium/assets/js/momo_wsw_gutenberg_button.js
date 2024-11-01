/*global momowsw_gutenberg_vars */
( function( wp ) {
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginPostStatusInfo = wp.editPost.PluginPostStatusInfo;
    var useState = wp.element.useState;
    var Spinner = wp.components.Spinner;
    const btnText = momowsw_gutenberg_vars.btnText;
    const postId = momowsw_gutenberg_vars.post_id;
    const btnType = momowsw_gutenberg_vars.btn_type;
    const shopifyId = momowsw_gutenberg_vars.shopify_id;
    const pType = momowsw_gutenberg_vars.ptype;
    // Function to add a button inside the "Document" sidebar
    var addCustomButtonToDocumentSidebar = function() {
        var [ isSpinnerVisible, setSpinnerVisible ] = useState( false );
        function handleDivClick() {
            setSpinnerVisible( true ); // Show the spinner
            // Your additional logic here (e.g., AJAX request)
    
            // Simulate asynchronous task
            setTimeout( function() {
                setSpinnerVisible( false ); // Hide the spinner
                alert('Div Clicked!');
            }, 2000 ); // Adjust the timeout duration as needed
        }
    
        /* return wp.element.createElement(
            PluginPostStatusInfo,
            null,
            wp.element.createElement(
                'div',
                {
                    className: 'momo-post-button full export-sidebar-content',
                    id: 'momo-wsw-export-to-shopify-others',
                    style: { width: '100%', textAlign: 'center'},
                    'data-post_id': postId,
					'data-type':btnType,
					'data-shopify_id':shopifyId,
                    'data-ptype':pType
                },
                isSpinnerVisible && wp.element.createElement( Spinner, { isVisibile: true, isLarge: true } ),
                !isSpinnerVisible && btnText // Show btnText when spinner is not visible
            )
        ); */
        return wp.element.createElement(
            PluginPostStatusInfo,
            null,
            wp.element.createElement(
                'div',
                {
                    className: 'momo-be-post-submitbox',
                    style: { width: '100%', textAlign: 'center' },
                },
                wp.element.createElement(
                    'div',
                    { className: 'momo-be-post-sb-message' }, // Inside momo-be-post-submitbox
                ),
                wp.element.createElement(
                    'div',
                    {
                        className: `momo-post-button full export-sidebar-content ${isSpinnerVisible ? 'loading' : ''}`,
                        id: 'momo-wsw-export-to-shopify-others',
                        style: { width: '100%', textAlign: 'center' },
                        /* onClick: handleDivClick, */ // Uncomment this line if you have a click handler
                        'data-post_id': postId,
                        'data-type': btnType,
                        'data-shopify_id': shopifyId,
                        'data-ptype': pType
                    },
                    wp.element.createElement('span', { className: 'momo-be-spinner' }), // Added the spinner element
                    wp.element.createElement('span', { className: 'momo-be-spinner-text' }, btnText), // Added the loading text
                     // Show btnText when spinner is not visible
                )
            )
        );
        
        
        
    };

    // Register the plugin with the enhanced "Document" sidebar
    registerPlugin( 'momowsw-export-sidebar', {
        render: addCustomButtonToDocumentSidebar,
        icon: 'admin-post',
    } );

} )( window.wp );
