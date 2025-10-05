<section class="content container">
    <h2 class="mb-4">Блог</h2>
    <div class='posts-block'>
        <?php 
            if (isset($data['posts']) && count($data['posts']) > 0) {
                foreach ($data['posts'] as $post) {
                    // ИСПРАВЛЕНО: используем -> вместо [] для объектов
                    $image = $post->image ? $post->image : 'public/img/not-found.png';
                    $title = $post->title ?? ($post->title ?? '');
                    $text = $post->text ?? ($post->content ?? ''); // возможно content вместо text
                    $date = $post->date ?? ($post->created_at ?? '');
                    
                    echo '
                        <div class="card mb-3">
                            <div class="row no-gutters">
                                <div class="col-md-4">
                                    <img src="/'.$image.'" class="card-img">
                                </div>
                                <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">'.$title.'</h5>
                                    <p class="card-text">'.$text.'</p>
                                    <p class="card-text"><small class="text-muted">'.$date.'</small></p>
                                </div>
                                </div>
                            </div>
                        </div>
                    ';
                }
            } else {
                echo "<div class='font-italic'>Тем нет</div>";
            }
        ?>
    </div>
    <nav class="mt-3 <?= isset($data['total_pages']) && $data['total_pages'] == 0 ? 'd-none' : '' ?>">
        <ul class="pagination justify-content-center">
            <li class="page-item">
                <a 
                    class="page-link" 
                    href="/blog/index/?page=<?= isset($data['current_page']) ? ($data['current_page'] - 1 == 0 ? 1 : $data['current_page'] - 1) : 1 ?>"
                >
                    Предыдущая
                </a>
            </li>
            <?php
                if (isset($data['total_pages'])) {
                    for ($i = 1; $i <= $data['total_pages']; $i++) {
                        $currentPage = $data['current_page'] ?? 1;
                        if (!($i == $currentPage)) {
                            echo '
                                <li class="page-item">
                                    <a class="page-link" href="/blog/index/?page='.$i.'">'.$i.'</a>
                                </li>
                            ';
                        } else {
                            echo '
                                <li class="page-item active">
                                    <a class="page-link" href="/blog/index/?page='.$i.'">'.$i.'</a>
                                </li>
                            ';
                        }
                    }
                }
            ?>
            <li class="page-item">
                <a 
                    class="page-link" 
                    href="/blog/index/?page=<?= 
                        isset($data['current_page'], $data['total_pages']) ? 
                        ($data['current_page'] + 1 > $data['total_pages'] ? $data['total_pages'] : $data['current_page'] + 1) : 
                        1 
                    ?>"
                >
                    Следующая
                </a>
            </li>
        </ul>
    </nav>
</section>