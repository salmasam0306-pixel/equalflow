from typing import Dict, Any, List
import logging

logger = logging.getLogger(__name__)

class ProjectHealthAnalyzer:
    def __init__(self):
        self.health_weights = {
            'completion_rate': 0.40,
            'overdue_rate': 0.30,
            'blocked_rate': 0.30
        }
    
    def analyze(self, total_tasks: int, completed_tasks: int, 
                overdue_tasks: int, blocked_tasks: int) -> Dict:
        """Analyze project health"""
        if total_tasks == 0:
            return {
                'status': 'empty',
                'health_score': 0,
                'message': 'No tasks in this project yet.',
                'recommendations': ['Start creating tasks to track progress.']
            }
        
        completion_rate = (completed_tasks / total_tasks) * 100
        overdue_rate = (overdue_tasks / total_tasks) * 100
        blocked_rate = (blocked_tasks / total_tasks) * 100
        
        health_score = 100
        health_score -= (blocked_rate * 0.3)
        health_score -= (overdue_rate * 0.4)
        health_score = max(0, min(100, health_score))
        
        status = self._get_health_status(health_score)
        recommendations = self._generate_recommendations(
            completion_rate=completion_rate,
            overdue_tasks=overdue_tasks,
            blocked_tasks=blocked_tasks
        )
        
        return {
            'total_tasks': total_tasks,
            'completed_tasks': completed_tasks,
            'completion_rate': round(completion_rate, 1),
            'overdue_tasks': overdue_tasks,
            'blocked_tasks': blocked_tasks,
            'health_score': round(health_score, 1),
            'status': status,
            'recommendations': recommendations
        }
    
    def _get_health_status(self, score: float) -> str:
        """Get health status based on score"""
        if score >= 80:
            return 'healthy'
        elif score >= 60:
            return 'at_risk'
        else:
            return 'critical'
    
    def _generate_recommendations(self, completion_rate: float, 
                                   overdue_tasks: int, blocked_tasks: int) -> List[str]:
        """Generate actionable recommendations"""
        recommendations = []
        
        if blocked_tasks > 0:
            recommendations.append(f"{blocked_tasks} blocked tasks need attention. Resolve dependencies or reassign.")
        
        if overdue_tasks > 0:
            recommendations.append(f"{overdue_tasks} overdue tasks. Consider adjusting deadlines or priorities.")
        
        if completion_rate < 50:
            recommendations.append(f"Completion rate is low ({completion_rate:.1f}%). Consider breaking down tasks or increasing resources.")
        
        if not recommendations:
            recommendations.append("Project is on track. Keep up the good work!")
        
        return recommendations