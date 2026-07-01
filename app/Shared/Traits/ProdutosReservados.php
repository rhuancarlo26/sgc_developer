<?php

namespace App\Shared\Traits;

trait ProdutosReservados
{
    /**
     * Slugs de produtos que já têm sistema de campanha próprio e dedicado
     * (não usam sgc_modulos/sgc_modulo_campanhas), então não podem ser
     * reaproveitados como produto_slug de um módulo. Os demais produtos
     * listados em Fauna.vue (patrimonio, indigena, quilombola, rima,
     * audiencia, pba, asv, viagens) não têm sistema próprio hoje - caem
     * num fallback genérico do Espeleologia - por isso podem ser vinculados
     * a um módulo normalmente.
     */
    protected function produtosReservados(): array
    {
        return [
            'fauna',
            'espeleologia',
            'pmqa',
            'eia',
        ];
    }

    /**
     * Produtos legados (mesma lista de Fauna.vue) que ainda não têm sistema
     * próprio e por isso podem ser escolhidos em "vincular a produto existente"
     * mesmo antes de qualquer módulo ser criado para eles.
     */
    protected function produtosLegadosLinkaveis(): array
    {
        return [
            ['produto_slug' => 'patrimonio', 'produto_titulo' => 'Patrimônio'],
            ['produto_slug' => 'indigena', 'produto_titulo' => 'Indígena'],
            ['produto_slug' => 'quilombola', 'produto_titulo' => 'Quilombola'],
            ['produto_slug' => 'rima', 'produto_titulo' => 'Rima'],
            ['produto_slug' => 'audiencia', 'produto_titulo' => 'Audiência'],
            ['produto_slug' => 'pba', 'produto_titulo' => 'PBA'],
            ['produto_slug' => 'asv', 'produto_titulo' => 'ASV'],
            ['produto_slug' => 'viagens', 'produto_titulo' => 'Viagens'],
        ];
    }
}
