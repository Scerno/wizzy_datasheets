import { registerBlockType } from '@wordpress/blocks';
import { __ }                from '@wordpress/i18n';
import { useSelect }         from '@wordpress/data';
import { SelectControl, PanelBody } from '@wordpress/components';
// import { useEffect }         from '@wordpress/element';
import { InspectorControls, InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import metadata              from './block.json';
import { useEntityProp } from '@wordpress/core-data';
import { TextControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useState, useEffect } from '@wordpress/element';

import './editor.css';   // editor-only outline
// import './style.css';    // front-end border


function useAllLayouts() {
        const [ layouts, setLayouts ] = useState( [] );

        useEffect( () => {
                apiFetch( {
                        path: '/wp/v2/datasheet_layout?per_page=-1&_fields=id,title.raw,datasheet_meta',
                        method: 'GET',
                        parse:  true,       // default
                        context: 'edit',    // sends nonce automatically
                } ).then( setLayouts );
        }, [] );

        return layouts;
};


const DatasheetEdit = ( { attributes, setAttributes } ) => {
	
	const layouts = useSelect(
		( select ) =>
			select( 'core' ).getEntityRecords(
				'postType',
				'datasheet_layout',
				{ per_page: -1, context: 'edit', _embed: true }
			),
		[]
	);
	
	// helper – returns the padding string once so you don’t repeat the logic
	const buildPadding = ( meta, unit ) => {
		return meta.margin_equal
			? `${ meta.margin }${ unit }`
			: `${ meta.margin_top }${ unit } ${ meta.margin_right }${ unit } ${ meta.margin_bottom }${ unit } ${ meta.margin_left }${ unit }`;
	};

    // Recompute style each time layout changes
    useEffect( () => {
        if ( ! layouts || ! attributes.layoutId ) return;

        const layout = layouts.find( ( l ) => l.id === attributes.layoutId );
        if ( ! layout ) return;
		
		console.info(layout);
		
        const meta   = layout?.datasheet_meta ?? {};
		
		console.info(meta);
		
		if ( ! meta || typeof meta !== 'object' || ! Object.keys( meta ).length ) {
			setAttributes( { styleString: '' } );
			return;
		}
		
		//guard against empty posts
		if ( ! meta ) {
			setAttributes( { styleString: '' } );
			return;            // meta not ready yet – abort calculations
		}
		
        const unit   = meta.margin_unit || 'mm';
        const w      = `${ meta.width  ?? 0 }${ meta.width_unit  ?? unit }`;
        const h      = `${ meta.height ?? 0 }${ meta.height_unit ?? unit }`;
		
		const style = {
			width:      `${ meta.width  ?? 0 }${ meta.width_unit  ?? unit }`,
			height:     `${ meta.height ?? 0 }${ meta.height_unit ?? unit }`,
			padding:    buildPadding( meta, unit ),
			boxSizing:  'content-box',
			};
	/*
        let style;
        if ( meta.margin_equal ) {
            style = `width:${ w };height:${ h };padding:${ meta.margin }${ unit };box-sizing:border-box;`;
        } else {
            style = `width:${ w };height:${ h };padding:${ meta.margin_top }${ unit } ${ meta.margin_right }${ unit } ${ meta.margin_bottom }${ unit } ${ meta.margin_left }${ unit };box-sizing:border-box;`;
        }
		
		style = {
			width: 40mm;
			height: 20mm;
			padding: 100px;
		};*/
		
		console.info(style);
        setAttributes( { styleString: style } );
    }, [ attributes.layoutId, layouts ] );

    const blockProps = useBlockProps( {
        className: 'datasheet-layout',
        style: attributes.styleString || undefined,
    } );
	
	// console.info(layouts);  

    return (
        <>
            <InspectorControls>
                <PanelBody title={ __( 'Datasheet settings', 'datasheets' ) } initialOpen={ true }>
                    <SelectControl
                        label={ __( 'Page Layout', 'datasheets' ) }
                        value={ attributes.layoutId }
                        options={ [
                            { label: __( '— Select —', 'datasheets' ), value: 0 },
                            ...( layouts || [] ).map( ( l ) => ({
                                label: l.title?.rendered || l.title?.raw || __( '(no title)', 'datasheets' ),
                                value: l.id,
                            }) ),
                        ] }
                        onChange={ ( v ) => setAttributes( { layoutId: parseInt( v, 10 ) } ) }
                    />
                </PanelBody>
            </InspectorControls>

            <div { ...blockProps }>
				<div className="datasheet-content">
					{ attributes.layoutId
						? <InnerBlocks />
						: <p>{ __( 'Choose a “Datasheet Layout” in the block settings.', 'datasheets' ) }</p>
					}
				</div>
            </div>
        </>
    );
};

registerBlockType( metadata.name, {
    ...metadata,
    edit: DatasheetEdit,
    save: () => <InnerBlocks.Content />   // keep inner blocks
} );
