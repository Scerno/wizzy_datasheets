/* src/blocks/page/index.js */
import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

import './style.css';   // editor-only outline

function PageEdit( { context } ) {
    const style  = context?.['datasheets/styleObj'] || {};
    const layout = context?.['datasheets/layoutId'];

    const blockProps = useBlockProps( {
        className: 'datasheet-page',
        style,
    } );

    return (
        <div {...blockProps }>
            { layout
                ? <InnerBlocks templateLock={ false } />
                : <p style={ { opacity: 0.5 } }>
                    Choose a layout in the parent block’s settings.
                  </p>
            }
        </div>
    );
}

registerBlockType( metadata.name, {
    edit: PageEdit,
    save: () => <InnerBlocks.Content />,
} );
