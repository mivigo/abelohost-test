{extends file="layout.tpl"}

{block name=content}
<div class="hero">
    <div class="container">
        <h1>Добро пожаловать на AbeloBlog</h1>
        <p>Узнавайте новые инсайды, статьи и полезные гайды по программированию, дизайну и бизнесу.</p>
    </div>
</div>

<div class="container">
    {foreach from=$categoriesData item=catData}
        <section class="category-section">
            <div class="category-header">
                <div class="category-info">
                    <h2>{$catData.model->name}</h2>
                    <p>{$catData.model->description}</p>
                </div>
                <a href="/category/{$catData.model->id}" class="btn btn-secondary">
                    Все статьи
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="posts-grid">
                {foreach from=$catData.posts item=post}
                    <article class="post-card">
                        <div class="post-image-container">
                            {if $post->img_path}
                                <img src="{$post->img_path}" alt="{$post->name}" class="post-image" loading="lazy">
                            {else}
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:#1e293b; color:#64748b;">No Image</div>
                            {/if}
                            <span class="post-meta-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                {$post->views}
                            </span>
                        </div>
                        <div class="post-content">
                            <span class="post-date">{$post->created_at|date_format:"%e %B %Y"}</span>
                            <h3 class="post-title">
                                <a href="/post/{$post->id}">{$post->name}</a>
                            </h3>
                            <p class="post-desc">{$post->description}</p>
                            
                            <div class="post-footer">
                                <a href="/post/{$post->id}" class="read-more">
                                    Читать полностью
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                {/foreach}
            </div>
        </section>
    {/foreach}
</div>
{/block}
