import { __ }                     from '@wordpress/i18n';
import { useSelect }              from '@wordpress/data';
import { SelectControl }          from '@wordpress/components';
import { useEffect }              from '@wordpress/element';
import { useBlockProps }          from '@wordpress/block-editor';

const DatasheetEdit = ( { attributes, setAttributes, clientId } ) => {

    // Get all layout posts (ID + title + meta) once.
    const layouts = useSelect(
        ( select ) =>
            select( 'core' ).getEntityRecords(
                'postType',
                'datasheet_layout',
                { per_page: -1, context: 'edit', _embed: true }
            ),
        []
    );

    // Compute styleString every time layoutId changes
    useEffect( () => {
        if ( ! layouts || ! attributes.layoutId ) return;

        const layout = layouts.find( ( l ) => l.id === attributes.layoutId );
        if ( ! layout ) return;

        const meta   = layout.meta._ds_layout_settings || {};
        const unit   = meta.margin_unit || 'mm';
        const width  = `${ meta.width  ?? 0 }${ meta.width_unit  ?? unit }`;
        const height = `${ meta.height ?? 0 }${ meta.height_unit ?? unit }`;

        let style;
        if ( meta.margin_equal ) {
            style = `width:${ width };height:${ height };padding:${ meta.margin }${ unit };box-sizing:border-box;`;
        } else {
            style = `width:${ width };height:${ height };padding:${ meta.margin_top }${ unit } ${ meta.margin_right }${ unit } ${ meta.margin_bottom }${ unit } ${ meta.margin_left }${ unit };box-sizing:border-box;`;
        }
        setAttributes( { styleString: style } );
    }, [ attributes.layoutId, layouts ] );

    const blockProps = useBlockProps( {
        className: 'datasheet-layout',
        style: attributes.styleString ? attributes.styleString : undefined,
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
                onChange={ ( val ) => setAttributes( { layoutId: parseInt( val, 10 ) } ) }
                __nextHasNoMarginBottom
            />
            <InnerBlocks />
        </div>
    );
};

export default DatasheetEdit;

/* Mandatory for block.json apiVersion 2 */
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
registerBlockType( metadata.name, {
    ...metadata,
    edit: DatasheetEdit,
    save: () => null,          // dynamic – PHP renders
} );
