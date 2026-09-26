from typing import List, Dict, Any
import logging

logger = logging.getLogger(__name__)

class WorkloadAnalyzer:
    def __init__(self):
        self.workload_thresholds = {
            'overloaded': 80,
            'busy': 60,
            'balanced': 30,
            'available': 0
        }
    
    def analyze(self, users: List) -> List[Dict]:
        """Analyze workload for a list of users"""
        results = []
        
        for user in users:
            workload = self._calculate_workload(user)
            status = self._get_workload_status(workload)
            
            results.append({
                'user_id': user.user_id,
                'workload_percentage': round(workload, 1),
                'active_tasks': user.active_tasks,
                'status': status,
                'recommendation': self._get_recommendation(workload, user.user_id)
            })
        
        results.sort(key=lambda x: x['workload_percentage'], reverse=True)
        return results
    
    def suggest_rebalance(self, users: List) -> List[Dict]:
        """Suggest task reassignment to balance workload"""
        overloaded = []
        underloaded = []
        
        for user in users:
            workload = self._calculate_workload(user)
            if workload >= 80:
                overloaded.append(user)
            elif workload <= 30:
                underloaded.append(user)
        
        suggestions = []
        for over_user in overloaded:
            if underloaded:
                suggestions.append({
                    'current_user_id': over_user.user_id,
                    'suggested_user_id': underloaded[0].user_id,
                    'reason': f"User {over_user.user_id} is overloaded ({self._calculate_workload(over_user):.1f}%). User {underloaded[0].user_id} has capacity.",
                    'priority': 'high' if self._calculate_workload(over_user) >= 90 else 'medium'
                })
        
        return suggestions
    
    def _calculate_workload(self, user) -> float:
        """Calculate workload percentage"""
        hours = user.active_tasks * 8
        return min((hours / 40) * 100, 100)
    
    def _get_workload_status(self, workload: float) -> str:
        """Get workload status label"""
        if workload >= 80:
            return 'overloaded'
        elif workload >= 60:
            return 'busy'
        elif workload >= 30:
            return 'balanced'
        else:
            return 'available'
    
    def _get_recommendation(self, workload: float, user_id: int) -> str:
        """Get recommendation text"""
        status = self._get_workload_status(workload)
        
        recommendations = {
            'overloaded': f"User {user_id} is overloaded. Consider reassigning tasks or extending deadlines.",
            'busy': f"User {user_id} is busy but manageable. Monitor workload closely.",
            'balanced': f"User {user_id} has a balanced workload.",
            'available': f"User {user_id} has capacity for more tasks. Consider assigning new work."
        }
        
        return recommendations.get(status, "")