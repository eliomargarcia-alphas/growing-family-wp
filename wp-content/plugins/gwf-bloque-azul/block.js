( function( blocks, element, editor, components, i18n ) {
    var el = element.createElement;
    var RichText = editor.RichText;
    var __ = i18n.__;
    
    // --- 1. Definición de Atributos ---
    var blockAttributes = {
        title: {
            type: 'string',
        },
        bodyText: {
            type: 'string',
            source: 'html',
            selector: 'p'
        }
    };
    
    blocks.registerBlockType( 'gwf-tarjetas/azul', {
        title: 'Tarjeta GWF Azul',
        icon: 'format-image',
        category: 'widgets',
        attributes: blockAttributes,
        
        // --- 2. Función de Edición (Backend) ---
        edit: function( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Estructura de la interfaz de edición
            return el( 
                'div', 
                { className: (props.className || '') + ' row align-items-center' },

                // TÍTULO
                el( RichText, {
                    tagName: 'h2',
                    value: attributes.title,
                    onChange: function( newTitle ) {
                        setAttributes( { title: newTitle } );
                    },
                    placeholder: __( 'Escribe el título aquí', 'gwf-tarjetas-azul' ),
                }),

                // TEXTO/PÁRRAFO
                el( RichText, {
                    tagName: 'p',
                    value: attributes.bodyText,
                    onChange: function( newBodyText ) {
                        setAttributes( { bodyText: newBodyText } );
                    },
                    placeholder: __( 'Añade el cuerpo del texto...', 'gwf-tarjetas-azul' ),
                })
            );
        },
        
        // --- 3. Función de Guardado (Frontend) ---
        save: function( props ) {
            var attributes = props.attributes;

            // Estructura HTML final que se guarda en el post
            return el( 
                'div', 
                { className: (props.className || '') + ' container py-3 px-4 px-xl-5 mt-5 bg-titanium border-radius-16' },

                    el( 'div', { className: 'row py-5 px-0 px-xl-5' },
                        // TÍTULO
                        attributes.title && el( RichText.Content, { tagName: 'h2', value: attributes.title, className: 'font-size-32 color-barium fw-semibold pb-4' } ),
                        
                        // TEXTO/PÁRRAFO
                        el( RichText.Content, { tagName: 'p', value: attributes.bodyText, className: 'font-size-20 color-barium' } )
                    ),
            );
        }
    } );
} )(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor,
    window.wp.components,
    window.wp.i18n
);