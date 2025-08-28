/* src/blocks/multipage/index.js */
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import {
	InnerBlocks,
	InspectorControls,
	useBlockProps
} from '@wordpress/block-editor';
import {
	PanelBody,
	RangeControl,
	SelectControl
} from '@wordpress/components';
import { useEffect } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';

const ALLOWED_CHILD = [ 'datasheets/page' ];

function MultipageEdit( { attributes, setAttributes, clientId } ) {
	const { pageCount, layoutId, containerStyle } = attributes;   // ← include layoutId

	const { insertBlocks, removeBlocks } = useDispatch( 'core/block-editor' );
	const childBlocks = useSelect( select =>
		select( 'core/block-editor' ).getBlock( clientId )?.innerBlocks || []
	);
	
	/* inside MultipageEdit ----------------------------- */

	const layouts = useSelect(
		( select ) =>
			select( 'core' ).getEntityRecords(
				'postType',
				'datasheet_layout',
				{ per_page: -1, context: 'edit', _embed: true }
			),
		[]
	); 

	useEffect( () => {
		if ( ! layouts || ! layoutId ) return;

		const layout = layouts.find( l => l.id === layoutId );
		const meta   = layout?.datasheet_meta || {};
		if ( ! Object.keys( meta ).length ) {
			setAttributes( { styleObj: {} } );
			return;
		}

		const unit = meta.margin_unit || 'mm';
		const newStyle = {
			width:  `${ meta.width }${ meta.width_unit }`,
			height: `${ meta.height }${ meta.height_unit }`,
			padding: meta.margin_equal
				? `${ meta.margin }${ unit }`
				: `${ meta.margin_top }${ unit } ${ meta.margin_right }${ unit } ${ meta.margin_bottom }${ unit } ${ meta.margin_left }${ unit }`,
			boxSizing: 'content-box',
		};
		const newContainerStyle  = {
			width:    newStyle.width,   // exact sheet width
			maxWidth: '100%',           // never exceed editor column
			padding: '0',
		};
		setAttributes( { styleObj: newStyle, containerStyle: newContainerStyle  } );
	}, [ layoutId, layouts ] );


	useEffect( () => {
		const diff = pageCount - childBlocks.length;

		if ( diff > 0 ) {
			for ( let i = 0; i < diff; i++ ) {
				insertBlocks(
					wp.blocks.createBlock( 'datasheets/page' ),
					childBlocks.length + i,
					clientId
				);
			}
		} else if ( diff < 0 ) {
			const toRemove = childBlocks.slice( diff );
			removeBlocks( toRemove.map( b => b.clientId ) );
		}
	}, [ pageCount ] );
	
	const blockProps = useBlockProps( {
		className: 'datasheet-multipage gap-8 flex flex-col',
		style: containerStyle || undefined,     // ← inline width + max-width
	} );

	return (
		<>
			<InspectorControls>

				<PanelBody title="Datasheet settings">
					<SelectControl
						label="Page layout"
						value={ layoutId }
						options={ [
							{ label: '— Select —', value: 0 },
							...( layouts || [] ).map( l => ( {
								label: l.title?.rendered || '(no title)',
								value: l.id,
							} ) ),
						] }
						onChange={ v => setAttributes( { layoutId: parseInt( v, 10 ) } ) }
					/>
					<RangeControl
						label="Number of pages"
						min={ 1 }
						max={ 20 }
						value={ pageCount }
						onChange={ value => setAttributes( { pageCount: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div className="datasheet-wrapper" style={{ maxWidth: '100%' }}>
				<div {...blockProps}>
					<div className="datasheet-content">
						<InnerBlocks
							allowedBlocks={ ALLOWED_CHILD }
							template={ Array.from( { length: pageCount }, () => [ 'datasheets/page' ] ) }
							templateLock="all"
						/>
					</div>
				</div>
			</div>
		</>
	);
}





/* ---------- register AFTER the component is declared ---------- */
registerBlockType( metadata.name, {
	edit:  MultipageEdit,          // ← was “Edit”, now the real function
	save: () => <InnerBlocks.Content />,
	attributes: metadata.attributes,
} );

export default MultipageEdit;      // keeps hot-reload happy
