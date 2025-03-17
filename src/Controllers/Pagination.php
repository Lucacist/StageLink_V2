<?php
class Pagination {
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    
    public function __construct($totalItems, $itemsPerPage = 10, $currentPage = 1) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = ceil($this->totalItems / $this->itemsPerPage);
        
        // Ajustement si page courante dépasse le total
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
    }
    
    public function getOffset() {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    public function getLimit() {
        return $this->itemsPerPage;
    }
    
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    public function getTotalPages() {
        return $this->totalPages;
    }
    
    public function hasPrevious() {
        return $this->currentPage > 1;
    }
    
    public function hasNext() {
        return $this->currentPage < $this->totalPages;
    }
    
    public function renderHtml($baseUrl = '') {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<div class="pagination-container">';
        $html .= '<ul class="pagination">';
        
        // Ajouter le paramètre page aux URLs existantes
        $urlPrefix = $baseUrl;
        if (strpos($baseUrl, '?') !== false) {
            $urlPrefix .= '&page=';
        } else {
            $urlPrefix .= '?page=';
        }
        
        // Bouton "Précédent"
        if ($this->hasPrevious()) {
            $html .= '<li><a href="' . $urlPrefix . ($this->currentPage - 1) . '" class="pagination-item">&laquo; Précédent</a></li>';
        } else {
            $html .= '<li><span class="pagination-item disabled">&laquo; Précédent</span></li>';
        }
        
        // Affichage des pages
        $startPage = max(1, $this->currentPage - 2);
        $endPage = min($this->totalPages, $startPage + 4);
        
        if ($startPage > 1) {
            $html .= '<li><a href="' . $urlPrefix . '1" class="pagination-item">1</a></li>';
            if ($startPage > 2) {
                $html .= '<li><span class="pagination-item ellipsis">...</span></li>';
            }
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            if ($i == $this->currentPage) {
                $html .= '<li><span class="pagination-item active">' . $i . '</span></li>';
            } else {
                $html .= '<li><a href="' . $urlPrefix . $i . '" class="pagination-item">' . $i . '</a></li>';
            }
        }
        
        if ($endPage < $this->totalPages) {
            if ($endPage < $this->totalPages - 1) {
                $html .= '<li><span class="pagination-item ellipsis">...</span></li>';
            }
            $html .= '<li><a href="' . $urlPrefix . $this->totalPages . '" class="pagination-item">' . $this->totalPages . '</a></li>';
        }
        
        // Bouton "Suivant"
        if ($this->hasNext()) {
            $html .= '<li><a href="' . $urlPrefix . ($this->currentPage + 1) . '" class="pagination-item">Suivant &raquo;</a></li>';
        } else {
            $html .= '<li><span class="pagination-item disabled">Suivant &raquo;</span></li>';
        }
        
        $html .= '</ul>';
        $html .= '<div class="pagination-info">Page ' . $this->currentPage . ' sur ' . $this->totalPages . '</div>';
        $html .= '</div>';
        
        return $html;
    }
}
