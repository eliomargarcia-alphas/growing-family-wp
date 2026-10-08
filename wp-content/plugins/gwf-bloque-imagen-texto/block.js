( function( blocks, element, blockEditor, components, i18n ) {
    var el = element.createElement;
    // La dependencia 'editor' fue reemplazada por 'blockEditor'
    var RichText = blockEditor.RichText;
    var MediaUpload = blockEditor.MediaUpload;
    var Button = components.Button;
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
        },
        imageUrl: {
            type: 'string',
            default: null
        }
    };
    
    blocks.registerBlockType( 'gwf-tarjetas/imagen-texto', {
        title: 'Tarjeta Imagen Texto GWF',
        icon: 'format-image',
        category: 'widgets',
        attributes: blockAttributes,
        
        // --- 2. Función de Edición (Backend) ---
        edit: function( props ) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Función para manejar la selección de la imagen
            var onSelectImage = function( media ) {
                setAttributes( { imageUrl: media.url } );
            };

            // Estructura de la interfaz de edición
            return el( 
                'div', 
                { className: (props.className || '') + ' row align-items-center' },

                // IMAGEN
                el( 'div', { className: 'col-xl-5' },
                    attributes.imageUrl ? (
                        // Si hay imagen, mostrarla y dar opción a eliminar
                        el( 'div', { className: 'image-preview' },
                            el( 'img', { src: attributes.imageUrl } ),
                            el( Button, {
                                isSecondary: true,
                                isSmall: true,
                                onClick: function() { setAttributes( { imageUrl: null } ); }
                            }, __( 'Eliminar imagen', 'gwf-tarjetas-imagen-texto' ) )
                        )
                    ) : (
                        // Si no hay imagen, mostrar el botón de subida
                        el( MediaUpload, {
                            onSelect: onSelectImage,
                            allowedTypes: [ 'image' ],
                            value: attributes.imageUrl,
                            render: function( obj ) {
                                return el( Button, {
                                    isPrimary: true,
                                    onClick: obj.open
                                }, __( 'Seleccionar Imagen', 'gwf-tarjetas-imagen-texto' ) );
                            }
                        } )
                    )
                ),

                // TÍTULO
                el( RichText, {
                    tagName: 'h2',
                    value: attributes.title,
                    onChange: function( newTitle ) {
                        setAttributes( { title: newTitle } );
                    },
                    placeholder: __( 'Escribe el título aquí', 'gwf-tarjetas-imagen-texto' ),
                }),

                // TEXTO/PÁRRAFO
                el( RichText, {
                    tagName: 'p',
                    value: attributes.bodyText,
                    onChange: function( newBodyText ) {
                        setAttributes( { bodyText: newBodyText } );
                    },
                    placeholder: __( 'Añade el cuerpo del texto...', 'gwf-tarjetas-imagen-texto' ),
                })
            );
        },
        
        // --- 3. Función de Guardado (Frontend) ---
        save: function( props ) {
            var attributes = props.attributes;

            // Estructura HTML final que se guarda en el post
            return el( 
                'div', 
                { className: (props.className || '') + ' row align-items-center' },

                // IMAGEN
                attributes.imageUrl && 
                    el( 'div', { className: 'col-xl-5' },
                        el( 'img', { src: attributes.imageUrl, alt: attributes.title, className: 'border-radius-16' } )
                    ),

                // div 7
                el( 'div', { className: 'col-xl-7 pt-4 pt-xl-0' },
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
    window.wp.blockEditor, // <-- Cambiado de wp.editor a wp.blockEditor
    window.wp.components,
    window.wp.i18n
);