import { registerBlockType } from '@wordpress/blocks';
import { __ }                from '@wordpress/i18n';
import { useSelect }         from '@wordpress/data';
import { SelectControl }     from '@wordpress/components';
import { useEffect }         from '@wordpress/element';
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';
import metadata              from './block.json';

const DatasheetEdit = ( { attributes, setAttributes } ) => {
    const layouts = useSelect( ( select ) =>
        select( 'core' ).getEntityRecords(
            'postType',
            'datasheet_layout',
            { per_page: -1, context: 'edit', _embed: true }
        ), []
    );

    useEffect( () => {
        if ( ! layouts || ! attributes.layoutId ) return;

        const layout = layouts.find( ( l ) => l.id === attributes.layoutId );
        if ( ! layout ) return;

        const meta   = layout.meta._ds_layout_settings || {};
        const unit   = meta.margin_unit || 'mm';
        const w      = `${ meta.width  ?? 0 }${ meta.width_unit  ?? unit }`;
        const h      = `${ meta.height ?? 0 }${ meta.height_unit ?? unit }`;

        let style;
        if ( meta.margin_equal ) {
            style = `width:${ w };height:${ h };padding:${ meta.margin }${ unit };box-sizing:border-box;`;
        } else {
            style = `width:${ w };height:${ h };padding:${ meta.margin_top }${ unit } ${ meta.margin_right }${ unit } ${ meta.margin_bottom }${ unit } ${ meta.margin_left }${ unit };box-sizing:border-box;`;
        }
        setAttributes( { styleString: style } );
    }, [ attributes.layoutId, layouts ] );

    const blockProps = useBlockProps( {
        className: 'datasheet-layout',
        style: attributes.styleString || undefined,
    } );

    return (
        <div { ...blockProps }>
            <SelectControl
                label={ __( 'Page Layout', 'datasheets' ) }
                value={ attributes.layoutId }
                options={ [
                    { label: __( '— Select —', 'datasheets' ), value: 0 },
                    ...( layouts || [] ).map( ( l ) => ({
                        label: l.title.render,
                        value: l.id,
                    }) ),
                ] }
                onChange={ ( v ) => setAttributes( { layoutId: parseInt( v, 10 ) } ) }
            />
            <InnerBlocks />
        </div>
    );
};

registerBlockType( metadata.name, {
    ...metadata,
    edit: DatasheetEdit,
    save: () => null      // dynamic: front-end rendered by PHP
} );
