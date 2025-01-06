import { registerBlockType } from '@wordpress/blocks';
import { useState, useEffect } from '@wordpress/element';
import { SelectControl } from '@wordpress/components';
import { InspectorControls } from '@wordpress/block-editor';
import './style.css';

registerBlockType('latest-posts-block/block', {
    title: 'Latest Posts Block',
    icon: 'editor-ul',
    category: 'widgets',
    attributes: {
        numberOfPosts: {
            type: 'number',
            default: 5
        }
    },
    edit: ({ attributes, setAttributes }) => {
        const [posts, setPosts] = useState([]);
        const [isLoading, setIsLoading] = useState(true);

        useEffect(() => {
            fetch(`/wp-json/latest-posts/v1/posts/?number=${attributes.numberOfPosts}`)
                .then(response => response.json())
                .then(data => {
                    setPosts(data);
                    setIsLoading(false);
                }).catch(err => {
                    console.log('error', err)
                });
        }, [attributes.numberOfPosts]);

        return (
            <div className="latest-posts-block">
                <InspectorControls>
                    <SelectControl
                        label="Number of Posts"
                        value={attributes.numberOfPosts}
                        options={[
                            { label: '3', value: 3 },
                            { label: '5', value: 5 },
                            { label: '10', value: 10 },
                        ]}
                        onChange={(value) => setAttributes({ numberOfPosts: parseInt(value) })}
                    />
                </InspectorControls>
                {isLoading ? (
                    <p>Loading...</p>
                ) : (
                    <div className="latest-posts-list">
                        {posts.map((post, index) => (
                            <div key={index} className="latest-post-item">
                                {post.featured_image && (
                                    <img src={post.featured_image} alt={post.title} className="post-featured-image" />
                                )}
                                <h3>{post.title}</h3>
                                <p>{post.excerpt}</p>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        );
    },
    save: () => {
        // Save is not necessary since we are using dynamic rendering
        return null;
    },
});
