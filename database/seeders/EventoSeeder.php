<?php

namespace Database\Seeders;

use App\Domain\Eventos\Enums\SituacaoEvento;
use App\Models\Congregacao;
use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = [
            ['ANIVERSSÁRIO DIRIGENTE', 'ANIVERSSÁRIO DIRIGENTE LÍLIO GASPAR', 'TABATINGA', '2024-02-19', 'agendado'],
            ['CASAMENTO UNIFICADO', 'CASAMENTO UNIFICADO - PROJETO QUE VISA CASAR O MÁXIMO DE CASAIS.', 'TABATINGA', '2024-06-22', 'agendado'],
            ['ENCONTRO PARA CASAIS - ESTUDO', 'AUXÍLIO AOS CASAIS ANTES DO CASAMENTO.', 'TABATINGA', '2024-04-01', 'agendado'],
            ['Aniversario do Pastor Presidente', null, 'SEDE', '2024-01-24', 'agendado'],
            ['Aniversario de posse do Pastor Presidente', null, 'SEDE', '2024-07-29', 'agendado'],
            ['Simpósio de Liderança', 'Pr. Jayro Kaillo, organizado pela CEAD.', 'SEDE', '2024-03-10', 'agendado'],
            ['Terceiro Encontro de Mulheres ADTC2 - NM1', 'Tema: Mulheres Edificadas na Palavra.', 'NOVO MARANGUAPE 1', '2024-05-25', 'agendado'],
            ['Inauguração do Templo da Congregação de Iracema', 'Início às 14:00.', 'IRACEMA', '2024-01-07', 'agendado'],
            ['Aniversário do Conjunto de Adolescentes - KADOSH', null, 'NOVO MARANGUAPE 1', '2024-05-07', 'agendado'],
            ['Aniversário de posse do Presbítero Luciano Alves', '6 anos de posse, às 19h.', 'SITIO SÃO LUIZ', '2024-09-09', 'agendado'],
            ['Aniversário da congregação Lameirão', null, 'LAMEIRÃO', '2024-03-23', 'agendado'],
            ['CAPACITAÇÃO PARA OBREIROS', 'Tema: Obreiro obediente, vida avivada.', 'TABATINGA', '2024-03-04', 'agendado'],
            ['CONFERÊNCIA AVIVA-NOS', 'Conferência de Jovens do Campo.', 'SEDE', '2024-02-12', 'agendado'],
            ['Aniversário do Conjunto de Senhoras Brilho Celeste', 'Culto de Aniversário.', 'PARQUE SÃO JOÃO', '2024-08-31', 'agendado'],
            ['Festa Renovadas por Cristo', 'Culto inicia às 18:30h.', 'NOVO PARQUE IRACEMA', '2024-06-01', 'agendado'],
            ['1° Tela Crente da UNA', 'Confraternização com adolescentes.', 'SEDE', '2024-03-30', 'agendado'],
            ['1° Conferência de Adolescentes', 'Organização: UNA.', 'SEDE', '2024-06-29', 'agendado'],
            ['Culto Especial - Dia da Mulher', 'Dia da Mulher.', 'SEDE', '2024-03-13', 'agendado'],
            ['35 anos do Conjunto Maranata', null, 'SEDE', '2024-05-09', 'agendado'],
            ['Congresso Unificado de Mulheres', 'Campo.', 'SEDE', '2024-10-19', 'agendado'],
            ['Festa do Círculo de Oração', null, 'SEDE', '2024-11-13', 'agendado'],
            ['Festa de Adolescentes - Leão de Judá', 'Aniversário do conjunto.', 'NOVO PARQUE IRACEMA', '2024-09-14', 'agendado'],
            ['Festa do Círculo de Oração', null, 'LAMEIRÃO', '2024-04-30', 'agendado'],
            ['Aniversário do Conjunto de Senhoras', 'Festa de Senhoras.', 'LAMEIRÃO', '2024-09-21', 'agendado'],
            ['Palestra para Adolescentes', null, 'SEDE', '2024-03-09', 'agendado'],
            ['Aniversário do conjunto Shalom', null, 'LAGES', '2024-11-19', 'agendado'],
            ['Aniversário do conjunto Vasos de Honra', null, 'LAGES', '2024-09-28', 'agendado'],
            ['Tarde de louvor e adoração para a juventude', 'Realizado pelo conjunto Shalom.', 'LAGES', '2024-07-07', 'agendado'],
            ['Seminário para adolescentes', 'À tarde.', 'LAGES', '2024-10-19', 'agendado'],
            ['Tarde de avivamento', null, 'LAGES', '2024-04-20', 'agendado'],
            ['Festa das crianças e Tocha Viva', 'Dia das crianças.', 'LAGES', '2024-10-26', 'agendado'],
            ['JANTAR DOS NAMORADOS', 'Alvo: líderes de casais.', 'SEDE', '2024-06-15', 'agendado'],
            ['Culto de Ação de Graças - Severino de Sousa', 'Às 19h.', 'PARQUE SÃO JOÃO', '2024-05-05', 'agendado'],
            ['Congresso da Família', null, 'SEDE', '2024-08-23', 'realizado'],
            ['Festa do Departamento Infantil', null, 'NOVO MARANGUAPE 1', '2024-07-09', 'agendado'],
            ['Festa do Departamento de Senhoras', null, 'NOVO MARANGUAPE 1', '2024-07-27', 'agendado'],
            ['Convivência de Senhoras', null, 'NOVO MARANGUAPE 1', '2024-10-05', 'agendado'],
            ['Jantar dos namorados - Tabatinga', 'Apenas para a congregação.', 'TABATINGA', '2024-06-12', 'agendado'],
            ['Encontro de Casais - NM2', null, 'NOVO MARANGUAPE 2', '2024-10-05', 'agendado'],
            ['Cruzada Moriá', 'Evento evangelístico.', 'NOVO MARANGUAPE 2', '2024-09-21', 'agendado'],
            ['Congresso para casais', null, 'SEDE', '2024-10-25', 'agendado'],
            ['Culto de Senhoras', 'Presidentes: Betânia, Sharly.', 'SEDE', '2024-10-12', 'agendado'],
            ['Conjunto Perfeito Louvor', null, 'NOVO MARANGUAPE 2', '2024-11-02', 'agendado'],
            ['Chá de Mulheres - NM2', null, 'NOVO MARANGUAPE 2', '2024-11-16', 'agendado'],
            ['Culto de Jovens do Setor Isaías', 'Parque São João.', 'PARQUE SÃO JOÃO', '2024-09-02', 'agendado'],
            ['Aniversário do Pastor Antônio das Graças', 'Evento na residência.', 'SEDE', '2024-11-04', 'agendado'],
            ['Aniversário do conjunto Reino de Deus', 'Festa das crianças.', 'LAMEIRÃO', '2024-10-12', 'agendado'],
            ['Aniversário Natalício - Ev. Carlos Neto', 'Culto de Gratidão.', 'NOVO MARANGUAPE 1', '2024-10-14', 'agendado'],
            ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2024-09-07', 'agendado'],
            ['Festa 22 Anos Harmonia Celeste', 'Aniversário conjunto de jovens.', 'NOVO PARQUE IRACEMA', '2024-12-14', 'agendado'],
            ['Evento Evangelístico - Lameirão', 'Programação todo o dia.', 'LAMEIRÃO', '2024-11-23', 'agendado'],
            ['Reunião para os Dirigentes', 'Às 14h na casa do Pastor Presidente.', 'SEDE', '2024-11-30', 'agendado'],
            ['Reunião com a Liderança da Sede', 'Às 19h na Sede.', 'SEDE', '2024-12-02', 'agendado'],
            ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2025-01-05', 'agendado'],
            ['Batismo nas águas', 'Início às 8h.', 'SEDE', '2025-09-07', 'agendado'],
            ['Festa de Posse do Pastor Presidente', null, 'SEDE', '2026-07-24', 'agendado'],
            ['Festa de Posse do Pastor Presidente', null, 'SEDE', '2025-07-23', 'agendado'],
            ['Aviva-nos', 'Juventude do campo. 03 e 04 de Março.', 'SEDE', '2025-03-03', 'agendado'],
            ['Aviva-nos', 'Juventude do campo.', 'SEDE', '2025-03-04', 'agendado'],
            ['Aniversário do Conjunto Acordes Celeste', '1 ano do departamento.', 'VITÓRIA', '2025-03-08', 'agendado'],
            ['CONFERÊNCIA DE ADOLESCENTES', 'Participação de todos os adolescentes.', 'SEDE', '2025-07-02', 'agendado'],
            ['2° Encontro de Mulheres', 'Com Aparecida Borges.', 'SEDE', '2025-06-21', 'agendado'],
            ['Workshop para Regentes', 'Treinamento e capacitação.', 'SEDE', '2025-05-24', 'agendado'],
            ['Capacitação para Professores de EBD', 'Todas as classes.', 'SEDE', '2025-04-12', 'agendado'],
            ['Festa do Conjunto de Crianças', 'Cordeirinhos de Cristo.', 'SEDE', '2025-12-17', 'agendado'],
            ['Encontro Para a Família', 'Palestras simultâneas.', 'SEDE', '2025-10-20', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-07', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-08', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-09', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-10', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-11', 'agendado'],
            ['Semana Missionária - Sede', 'Evangelismo em massa.', 'SEDE', '2025-10-12', 'agendado'],
            ['Conferência Missionária', 'Encerramento.', 'SEDE', '2025-10-13', 'agendado'],
            ['Aniversário do conjunto de senhoras', 'Brilhando com Cristo.', 'TABATINGA', '2025-07-08', 'agendado'],
            ['Encontro de Mulheres - Tabatinga', 'Provérbios 31:25.', 'TABATINGA', '2025-04-22', 'agendado'],
            ['Simpósio de casais', null, 'TABATINGA', '2025-05-05', 'agendado'],
            ['Simpósio de casais', null, 'TABATINGA', '2025-05-06', 'agendado'],
            ['Simpósio de casais', null, 'TABATINGA', '2025-05-07', 'agendado'],
            ['Seminário Missionário', null, 'TABATINGA', '2025-07-09', 'agendado'],
            ['Seminário Missionário', null, 'TABATINGA', '2025-07-10', 'agendado'],
            ['Seminário Missionário', null, 'TABATINGA', '2025-07-11', 'agendado'],
            ['Cruzada Evangelística - Gererau', null, 'TABATINGA', '2025-07-12', 'agendado'],
            ['Congresso Missionário', null, 'TABATINGA', '2025-07-15', 'agendado'],
            ['Congresso Missionário', null, 'TABATINGA', '2025-07-16', 'agendado'],
            ['Congresso Missionário', null, 'TABATINGA', '2025-07-17', 'agendado'],
            ['Congresso Missionário', null, 'TABATINGA', '2025-07-18', 'agendado'],
            ['Cruzada Evangelística - Sapupara', null, 'TABATINGA', '2025-07-19', 'agendado'],
            ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-07', 'agendado'],
            ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-08', 'agendado'],
            ['Preparação para Obreiros', null, 'TABATINGA', '2025-04-09', 'agendado'],
            ['Encontro de Casais - Tabatinga', null, 'TABATINGA', '2025-06-12', 'agendado'],
            ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-12', 'agendado'],
            ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-13', 'agendado'],
            ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-14', 'agendado'],
            ['Congresso Geral Unificado', 'Do dia 12 ao 15.', 'SEDE', '2025-11-15', 'agendado'],
            ['Chá de mulheres - NM1', '1° chá de 2025, às 15:30h.', 'NOVO MARANGUAPE 1', '2025-04-19', 'agendado'],
            ['Festa de crianças - NM1', null, 'NOVO MARANGUAPE 1', '2025-10-27', 'agendado'],
            ['Festa de adolescentes - NM1', null, 'NOVO MARANGUAPE 1', '2025-06-24', 'agendado'],
            ['Festa das Crianças - Novo Parque Iracema', null, 'NOVO PARQUE IRACEMA', '2025-08-11', 'agendado'],
            ['Palestra - Sede', 'Das 15h às 16h.', 'SEDE', '2025-03-22', 'agendado'],
            ['Chá com Elas - NM2', 'Das 15h às 16h.', 'NOVO MARANGUAPE 2', '2025-03-15', 'agendado'],
            ['Seminário Para Obreiros - Lages', null, 'LAGES', '2025-05-10', 'agendado'],
            ['Aniversário de Posse e Natalício - Ev. René', null, 'LAGES', '2025-04-28', 'agendado'],
            ['23° Aniversário de Senhoras', 'Renovadas por Cristo.', 'NOVO PARQUE IRACEMA', '2025-10-14', 'agendado'],
            ['Cruzada na SEDE', 'Cruzada evangelística, às 19h.', 'SEDE', '2026-08-22', 'agendado'],
            ['Congresso de missões - Lameirão', '1º congresso de missões.', 'LAMEIRÃO', '2026-11-21', 'agendado'],
            ['Congresso de missões - Lameirão', '1º congresso de missões.', 'LAMEIRÃO', '2026-11-22', 'agendado'],
            ['Culto festivo 24 anos do departamento de jovens', 'Rua Oliveira Cavalcante, 167.', 'NOVO MARANGUAPE 1', '2026-08-22', 'agendado'],
            ['Culto de ação de graças - Ev. Carlos Neto', 'Aniversário de posse e natalício.', 'NOVO MARANGUAPE 1', '2026-10-05', 'agendado'],
            ['Culto festivo 24 anos do círculo de oração', 'Rua Oliveira Cavalcante, 167.', 'NOVO MARANGUAPE 1', '2026-12-01', 'agendado'],
            ['Culto festivo 21 anos Brilho Celeste', 'Às 18:30h.', 'PARQUE SÃO JOÃO', '2026-10-20', 'agendado'],
            ['TARDE DE LOUVOR - SENHORAS', 'Todo o campo, às 14h.', 'SEDE', '2026-10-31', 'agendado'],
            ['CÍRCULO DE ORAÇÃO - Nível Campo', null, 'SEDE', '2026-12-28', 'agendado'],
            ['RETIRO DE JOVENS', 'Nível campo. 31/out a 02/nov.', 'SEDE', '2026-10-31', 'agendado'],
            ['RETIRO DE JOVENS', 'Nível campo.', 'SEDE', '2026-11-01', 'agendado'],
            ['RETIRO DE JOVENS', 'Nível campo.', 'SEDE', '2026-11-02', 'agendado'],
            ['ANIVERSÁRIO DO CONJUNTO FONTES DE ÁGUAS VIVAS', 'Nível campo.', 'SEDE', '2026-08-05', 'agendado'],
            ['ANIVERSÁRIO DO CONJUNTO HERDEIROS DO REINO', 'Todo o campo convocado.', 'SEDE', '2026-12-16', 'agendado'],
            ['ENCONTRO DE CASAIS - SEDE', 'Somente para a Sede.', 'SEDE', '2026-08-15', 'agendado'],
            ['CONGRESSO DA FAMÍLIA - SEDE', '13 e 14 de novembro.', 'SEDE', '2026-11-13', 'agendado'],
            ['CONGRESSO DA FAMÍLIA - SEDE', '13 e 14 de novembro.', 'SEDE', '2026-11-14', 'agendado'],
            ['Convivência de Jovens - Vitória', 'Parceria entre congregações.', 'VITÓRIA', '2026-09-19', 'agendado'],
            ['Congresso Avivamento e Restauração', '31/out a 02/nov.', 'LAGES', '2026-10-31', 'agendado'],
            ['Convivência das Filhas de Sião', 'Liderança do departamento.', 'NOVO MARANGUAPE 1', '2026-10-24', 'agendado'],
            ['Culto festivo 9 anos infantil', 'Cordeirinhos de Cristo.', 'NOVO MARANGUAPE 3', '2026-10-24', 'agendado'],
            ['Festa de Jovens - 32 anos Brilho Celeste', '32° Aniversário.', 'VITÓRIA', '2026-11-23', 'agendado'],
            ['REUNIÃO LIDERANÇA DA UCADEM', 'Líderes de crianças.', 'SEDE', '2026-12-05', 'agendado'],
            ['Cruzada: Jubaia para Cristo', 'Às 19h.', 'JUBAIA', '2026-10-17', 'agendado'],
            ['Confraternização de Dirigentes e Esposas', 'Em Jubaia.', 'SEDE', '2026-12-12', 'agendado'],
            ['Congresso de Crianças - Jubaia', 'Às 18h.', 'JUBAIA', '2026-11-28', 'agendado'],
        ];

        $congregacoes = Congregacao::pluck('id', 'nome');
        $situacoesValidas = array_column(SituacaoEvento::cases(), 'value');

        foreach ($eventos as [$evento, $anotacoes, $cong, $dt, $sit]) {
            if (! isset($congregacoes[$cong])) {
                continue;
            }

            $situacao = in_array($sit, $situacoesValidas, true)
                ? $sit
                : SituacaoEvento::Agendado->value;

            Evento::create([
                'evento' => $evento,
                'anotacoes' => $anotacoes ?: null,
                'congregacao_id' => $congregacoes[$cong],
                'dt_evento' => $dt,
                'situacao' => $situacao,
            ]);
        }
    }
}
